<?php
require __DIR__ . "/../index.php";


require PROJECT_ROOT_PATH . "/controller/api/UserController.php";
require PROJECT_ROOT_PATH . "/controller/api/EmployeeController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
    //   "employeeId"=>Array("string", "required"),
    //   "username"=>Array("string", "optional"),
    //   "email"=>Array("string", "optional"),
    //   "fullname"=>Array("string", "optional"),
    //   "gender"=>Array("number", "optional"),
    //   "birthDate"=>Array("string", "optional"),
    //   "enrollDate"=>Array("string", "optional"),
    //   "contactNo"=>Array("string", "optional"),
    //   "contactPerson"=>Array("string", "optional"),
    //   "role"=>Array("string", "optional"),
    //   "department"=>Array("string", "optional"),
        "condition"=>Array("object", "required"),
            // "username"=>Array("string", "optional"),
            // "employee_id"=>Array("string", "optional"),

        "updateData"=>Array("array", "optional", "key"=>Array(
            "employeeId"=>Array("string", "optional"),
            "username"=>Array("string", "optional"),
            "email"=>Array("string", "optional"),
            "fullname"=>Array("string", "optional"),
            "gender"=>Array("number", "optional"),
            "birthdate"=>Array("string", "optional"),
            "enrollDate"=>Array("string", "optional"),
            "contactNo"=>Array("string", "optional"),
            "contactPerson"=>Array("string", "optional"),
            "role"=>Array("string", "optional"),
            "department"=>Array("string", "optional"),
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
$apiController = new EmployeeController();
$result = $apiController->{'updateEmployee'}( $reqBody["condition"], $reqBody["updateData"]);
// echo "Result  **** ", $result;
if($result["error"] == null) {
    responseSuccess($result ? 200:304, $result ? "Success" : "No data updated", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>