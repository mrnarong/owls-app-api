<?php
require __DIR__ . "/../../index.php";
require PROJECT_ROOT_PATH . "/controllers/api/CommonController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = Array(
    "payload" => Array(
      "refNo"=>Array("number", "required"),
      "company"=>Array("string", "required"),
      "employeeId"=>Array("string", "required"),
      "type"=>Array("string", "required"),
      "status"=>Array("string", "required"),
      "approvedBy"=>Array("string", "required"),
      // "approveDate"=>Array("number", "required"),
    ),
    "headers" => Array(
      // "Authorization"=>Array("string", "required"),
      "Apikey"=>Array("string", "required")
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

$company = $reqBody["company"];
unset($reqBody["company"]);
$apiController = new CommonController();

// modify approvedBy
// $reqBody["approvedBy"] ==> email to username
$reqBody["approvedBy"] = "test";
$result = $apiController->approveRequest($company, $reqBody);

// print_r($reqBody["company"]);
// print_r($employeeId);

if(!isset($result["error"])) {
  responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>