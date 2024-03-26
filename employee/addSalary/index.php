<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
require PROJECT_ROOT_PATH . "/controllers/api/EmployeeController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = array(
    "payload" => array(
        "company" => array("string", "required"),
        "salaryData"=>Array("array", "optional", "key"=>Array(
            "employeeId" => array("string", "required"),
            "department" => array("string", "required"),
            "position" => array("string", "required"),
            "salary" => array("number", "required"),
            "effectiveDate" => array("string", "required"),

            "note" => array("string", "optional", "empty"),
            "status" => array("string", "optional", "empty"),
        ))
    ),
    "headers" => array(
        "Authorization" => array("string", "required"),
        //   "ApiKey"=>Array("string", "required")
    )
);


$validation = new Validation;
$vdResult = $validation->checkHeader($reqConfig["headers"], getallheaders());
if ($vdResult["message"] !== "Ok") {
    responseError(442, $vdResult["status"], $vdResult["message"], null);
}

$vdResult = $validation->checkPayload($reqConfig["payload"], $reqBody);
if ($vdResult["message"] !== "Ok") {
    responseError(442, $vdResult["status"], $vdResult["message"], null);
}

$mapFileds = array(
    "company" => "company",
    "employeeId" => "employee_id",
    "department" => "department",
    "position" => "position",
    "salary" => "salary",
    "effectiveDate" => "effective_date",

    "note" => "note",
    "status" => "status",
);


$company = $reqBody["company"];
$apiController = new EmployeeController();
$result = $apiController->addSalary( 
    $company, 
    $reqBody["salaryData"]["employeeId"], $reqBody["salaryData"]["salary"], 
    $reqBody["salaryData"]["department"], $reqBody["salaryData"]["position"], 
    $reqBody["salaryData"]["effectiveDate"], $reqBody["salaryData"]["note"], 
    $reqBody["salaryData"]["status"]);
// print_r($result);
if (!isset($result["error"])) {
    responseSuccess($result ? 200 : 304, $result ? "Success" : "No data updated", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
