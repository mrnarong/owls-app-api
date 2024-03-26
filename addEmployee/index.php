<?php
require __DIR__ . "/../index.php";


require PROJECT_ROOT_PATH . "/controller/api/UserController.php";
require PROJECT_ROOT_PATH . "/controller/api/EmployeeController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
        "company"=>Array("string", "optional"),

        "username"=>Array("string", "required"),
        "role"=>Array("string", "required"),

        "email"=>Array("string", "required"),
        "fullname"=>Array("string", "required"),
        "fullnameEn"=>Array("string", "required"),
        "idCardNo"=>Array("string", "required"),
        "gender"=>Array("number", "required"),
        "birthDate"=>Array("string", "required"),
        "address"=>Array("string", "required"),

        "contactNo"=>Array("string", "required"),
        "contactPerson"=>Array("string", "required"),

        "employeeId"=>Array("string", "required"),
        "enrollDate"=>Array("string", "required"),
        "department"=>Array("string", "required"),
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
$company = $reqBody["company"] ? $reqBody["company"] : getTokenCompany();
$apiController = new EmployeeController();
$result = $apiController->{'addEmployee'}( $company, $reqBody);

if($result == true && $reqBody["addActivateUser"]) {
    $uuid = guidv4();
    $apiController = new UserController();
    $result = $apiController->{'addActivateUser'}( $company, Array(
        "token"=>$uuid, 
        "email"=>$reqBody["email"], 
    ));
    if($result == true) {
        // Send activate mail...
    }
}

if($result["error"] == null) {
    responseSuccess(200, "Success", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>