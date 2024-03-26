<?php
require __DIR__ . "/../index.php";
// 
require PROJECT_ROOT_PATH . "/controller/api/UserController.php";
require PROJECT_ROOT_PATH . "/controller/api/EmployeeController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = Array(
    "payload" => Array(
      "username"=>Array("string", "required"),
      "password"=>Array("string", "required"),
    //   "companyId"=>Array("string", "required"),
    //   "fullname"=>Array("string", "required"),
    //   "mobileNo"=>Array("number", "required"),
    //   "token"=>Array("string", "required"),
    //   "role"=>Array("string", "required"),
    //   "status"=>Array("string", "required"),
    ),
    "headers" => Array(
    //   "Authorization"=>Array("string", "required"),
      "ApiKey"=>Array("string", "required")
      )
);

$validation = new Validation;
$vdResult = $validation->checkHeader($reqConfig["headers"], getallheaders());
if($vdResult["message"]!=="Ok"){
    responseError(442, $vdResult["status"], $vdResult["message"], null);
}

$vdResult = $validation->checkPayload($reqConfig["payload"], $reqBody);
if($vdResult["message"]!=="Ok"){
    responseError(442, $vdResult["status"], $vdResult["message"], null);
}
$apiController = new UserController();
$result = $apiController->{'login'}($reqBody);
$company = $result[0]['company'];
if(is_array($result) && sizeof($result)) {
    $apiController = new EmployeeController();
    $result = $apiController->{'getEmployee'}($company, Array("employee_id"=>$result[0]["employee_id"]));
    // print_r($result);
    if(is_array($result) && sizeof($result)) {
        $jwt = new JWT();
        $iat = time();
        $info = array(
            "username"  => $result[0]["username"],
            "email"     => $result[0]["email"],
            "fullname"  => $result[0]['fullname'],
            "role"      => $result[0]['role'],
            "company"   => $company,
            "iat"       => $iat,
            "exp"       => $iat + 7 * 24 * 60 * 60 // 7 days
        );
        $access_token   = $jwt->encode($info, SECRET_KEY);
        $info["exp"]    = $info["exp"] - 30 * 60; // 30 mins
        $refresh_token  = $jwt->encode($info, SECRET_KEY);
    }
}


if($result["error"] == null) {
    if($access_token) {
        responseSuccess(200, "Success", Array("accessToken"=>$access_token, "refreshToken"=>$refresh_token));
    } else {
        responseSuccess(401, "Login failed", null);
    }
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>