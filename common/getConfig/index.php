<?php
require __DIR__ . "/../../index.php";

require PROJECT_ROOT_PATH . "/controllers/api/ConfigController.php";

$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r($reqBody);
//-------------- Req body validation --------------
$reqConfig = Array(
    "payload" => Array(
      "configName"=>Array("string", "required"),
    ),
    "headers" => Array(
      "Authorization"=>Array("string", "required"),
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


$apiController = new ConfigController();
$result = $apiController->getConfig(Array("config_name"=>$reqBody["configName"]));


if(!isset($result["error"])) {
    responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}

?>