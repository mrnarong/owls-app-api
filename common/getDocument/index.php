<?php
require __DIR__ . "/../../index.php";
require PROJECT_ROOT_PATH . "/controllers/api/CommonController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = Array(
    "payload" => Array(
      "company"=>Array("string", "optional"),
      // "username"=>Array("string", "optional"),
      "employeeId"=>Array("string", "required"),
      "docName"=>Array("string", "required"),
      // "docType"=>Array("string", "required"),
      "recId"=>Array("number", "optional"),
      "docIdx"=>Array("number", "optional"),
    //   "token"=>Array("string", "required"),
    //   "role"=>Array("string", "required"),
    //   "status"=>Array("string", "required"),
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

$company = $reqBody["company"];
unset($reqBody["company"]);
$apiController = new CommonController();
$result = $apiController->getDocument($company, $reqBody);

// print_r($reqBody["company"]);
// print_r($employeeId);

if(!isset($result["error"])) {
  responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>