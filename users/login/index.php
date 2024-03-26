<?php
// echo __DIR__ . "/../index.php";

require __DIR__ . "/../../index.php";
// 

require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
require PROJECT_ROOT_PATH . "/controllers/api/EmployeeController.php";


$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = Array(
    "payload" => Array(
      "username"=>Array("string", "required"),
      "password"=>Array("string", "required"),
    //   "companyId"=>Array("string", "required"),
    ),
    "headers" => Array(
    //   "Authorization"=>Array("string", "required"),
      "Apikey"=>Array("string", "required")
      )
);
// sleep(2);
// print_r(getallheaders());
$validation = new Validation;
$vdResult = $validation->checkHeader($reqConfig["headers"], getallheaders());
if($vdResult["message"]!=="Ok"){
    responseError(442, $vdResult["status"], $vdResult["message"], null);
}

$vdResult = $validation->checkPayload($reqConfig["payload"], $reqBody);
if($vdResult["message"]!=="Ok"){
    responseError(442, $vdResult["status"], $vdResult["message"], null);
}
$responseData = Array();
$apiController = new UserController();
$result = $apiController->login($reqBody);
$company = $result[0]['company'];
$role = $result[0]['role'];
$reset = $result[0]['reset'];
$username = $result[0]['username'];
$email = $result[0]['user_email'];
$authMenu = $result[0]['auth_menu'];
// print_r($result);
if(is_array($result) && sizeof($result)) {
    $apiController = new EmployeeController();
    $result = $apiController->getEmployee($company, Array("condition"=>Array(Array("employee_id"=>$result[0]["employee_id"]))));
    // print_r($result);
    $jwt = new JWT();
    $iat = time();
    $info = false;
    if(is_array($result) && sizeof($result)) {
        // $jwt = new JWT();
        // $iat = time();
        $info = array(
            "username"  => $result[0]["username"],
            "email"     => $result[0]["email"],
            "fullname"  => $result[0]['fullname'],
            "fullnameEn"  => $result[0]['fullnameEn'],
            "role"      => $role, //$result[0]['role'],
            "employeeId"  => $result[0]["employeeId"],
            "department"  => $result[0]["department"],
            "authMenu"  => $authMenu,
            "company"   => $company,
            "iat"       => $iat,
            "exp"       => $iat + 7 * 24 * 60 * 60 // 7 days
        );
        // $responseData["accessToken"] = $jwt->encode($info, SECRET_KEY);
        // // $access_token   = $jwt->encode($info, SECRET_KEY);
        // $info["exp"]    = $info["exp"] - 30 * 60; // 30 mins
        // $responseData["refreshToken"] = $jwt->encode($info, SECRET_KEY);
        // if($reset) {
        //     $responseData["reset"] = true;
        // }
    } else {
        // echo "Employee not found";
        $info = array(
            "username"  => $username,
            "email"     => $email,
            "fullname"  => $role === "ADMIN" ? "Admin" : ($role === "SUPERADMIN" ? "Super Admin" : ""),
            "fullnameEn"  => $role === "ADMIN" ? "Admin" : ($role === "SUPERADMIN" ? "Super Admin" : ""),
            "role"      => $role,
            "authMenu"  => $authMenu,
            "company"   => $company,
            "iat"       => $iat,
            "exp"       => $iat + 7 * 24 * 60 * 60 // 7 days
        );
    }
    if($info) {
        $responseData["accessToken"] = $jwt->encode($info, SECRET_KEY);
        // $access_token   = $jwt->encode($info, SECRET_KEY);
        $info["exp"]    = $info["exp"] - 30 * 60; // 30 mins
        $responseData["refreshToken"] = $jwt->encode($info, SECRET_KEY);
        if($reset) {
            $responseData["reset"] = true;
        }
    }
}



if(!isset($result["error"])) {
    if($info){ //$responseData["accessToken"]) {
        responseSuccess(200, "Success", $responseData);
    } else {
        responseSuccess(401, "Login failed", null);
    }
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>