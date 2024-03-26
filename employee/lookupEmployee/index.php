<?php
require __DIR__ . "/../../index.php";
// 
require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
require PROJECT_ROOT_PATH . "/controllers/api/EmployeeController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = array(
  "payload" => array(
    "company" => array("string", "optional", "empty"),
    "condition" => Array("object", "required"),
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

$mapSortFileds = array(
  "company" => "company",
  "userEmail" => "email",
  "username" => "username",
  "employeeId" => "employee_id",
  "idNumber" => "id_number",
);
$reqParams = array();
$conditions = array();

if($reqBody["condition"]) {
  // echo "Condition";
  foreach ($reqBody["condition"] as $elCond) {
      $mappedEl = Array();
      foreach ($elCond as $key => $value) {
          if($mapSortFileds[$key]) {
              $mappedEl[$mapSortFileds[$key]] = $value;
          }
      }
      if($mappedEl) {
          array_push($conditions, $mappedEl);
      }
  }
}

$reqParams["condition"] = $conditions ? $conditions : [];

$company = $reqBody["company"] ? $reqBody["company"] : getTokenCompany();
$apiController = new EmployeeController();
$result = $apiController->{'lookupEmployee'}($company, $reqParams);


if (!isset($result["error"])) {
  responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
} else {
  responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
