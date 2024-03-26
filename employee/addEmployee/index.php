<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
require PROJECT_ROOT_PATH . "/controllers/api/EmployeeController.php";
require PROJECT_ROOT_PATH . "/controllers/api/PayrollController.php";
require_once(PROJECT_ROOT_PATH . "/inc/utils.php");
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));



$reqConfig = Array(
    "payload" => Array(
        "company"=>Array("string", "required"),

        "employeeId"=>Array("string", "required"),
        "fullname"=>Array("string", "required"),
        "fullnameEn"=>Array("string", "required"),
        "mobileNo"=>Array("string", "required"),
        "birthDate"=>Array("string", "required"),
        "idNumber"=>Array("string", "required"),
        "gender"=>Array("string", "required"),

        "enrollDate"=>Array("string", "required"),
        "position"=>Array("string", "required"),
        "department"=>Array("string", "required"),
        "salary"=>Array("number", "required"),
        "employType"=>Array("string", "required"),
        "paymentType"=>Array("string", "required"),
        "bank"=>Array("string", "required"),
        "bankAccNo"=>Array("string", "required"),


        "username"=>Array("string", "required"),
        "email"=>Array("string", "required"),
        "password"=>Array("string", "optional"),
        "role"=>Array("string", "required"),

        "contactNo"=>Array("string", "optional", "empty"),
        "contactPerson"=>Array("string", "optional", "empty"),
        "relation"=>Array("string", "optional", "empty"),

        "documents"=>Array("array", "optional", "empty"),
        "address"=>Array("string", "optional"),
        "addActivateUser"=>Array("boolean", "optional"),

    ),
    "headers" => Array(
        "Authorization"=>Array("string", "required"),
    //   "ApiKey"=>Array("string", "required")
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

// $company = getTokenData("company");
// echo "Add employee: $company, $user";


$apiController = new EmployeeController();
$result = $apiController->addEmployee( $reqBody["company"], $reqBody);
if($result == true) {


    $user = getTokenData("username");
    $apiController = new EmployeeController();
    $result = $apiController->addSalary($reqBody["company"], $reqBody["employeeId"], $reqBody["salary"], $reqBody["department"], $reqBody["position"], $reqBody["enrollDate"], "Add New Employee", "WAITING");

    $apiController = new UserController();
    
    $result = $apiController->addUser( Array(
        "username"=>$reqBody["username"], 
        "email"=>$reqBody["email"], 
        "employeeId"=>$reqBody["employeeId"], 
        "password"=>$reqBody["password"], 
        "company"=>$reqBody["company"], 
        "role"=>$reqBody["role"],
        "roleAuthGroup"=>strtoupper($reqBody["company"])."_USR_".strtoupper($reqBody["department"]),

    ));
    


    if($result == true){

        $util = new Utils();
        $result = $util->sendActivateMail("HR@owlswallpapers.com", $reqBody["email"], $reqBody["username"], $reqBody["password"]);
        // $result = Array();
        // Send activate mail...
    } else {
        $result = Array("code"=>"500", "message"=>"Add user failed");
    }
}

if(!isset($result["error"])) {
    responseSuccess($result["code"]? $result["code"] : 200, $result["message"] ? $result["message"] : "Success", null);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>