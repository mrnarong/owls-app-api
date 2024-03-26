<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
require PROJECT_ROOT_PATH . "/controllers/api/EmployeeController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
        "recId"=>Array("number", "required"),
        "updateData"=>Array("array", "optional", "key"=>Array(
            // "employeeId"=>Array("string", "optional"),
            "department"=>Array("string", "optional"),
            "position"=>Array("string", "optional"),
            "salary"=>Array("number", "optional"),
            "effectiveDate" => array("string", "optional"),

            "note"=>Array("string", "optional", "empty"),
            "status"=>Array("string", "optional", "empty"),
        )),

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

$mapFileds = Array(
    "recId"=>"rec_id",
    "company"=>"company",
    "employeeId"=>"employee_id",
    "username"=>"username",
    "email"=>"email",
    "fullname"=>"fullname",
    "gender"=>"gender",
    "birthdate"=>"birthdate",
    "enrollDate"=>"enroll_date",
    "contactNo"=>"contact_no",
    "contactPerson"=>"contact_person",
    "role"=>"role",
    "department"=>"department",
);


$company = $reqBody["company"];
$apiController = new EmployeeController();
$result = $apiController->updateSalary( $reqBody["recId"], $reqBody["updateData"]);
// print_r($result);
if(!isset($result["error"])) {
    responseSuccess($result ? 200:304, $result ? "Success" : "No data updated", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>