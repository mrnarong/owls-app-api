<?php
require __DIR__ . "/../../index.php";
// 
require PROJECT_ROOT_PATH . "/controllers/api/PayrollController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = Array(
    "payload" => Array(
        "monthYear"=>Array("string", "required"),
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

$apiController = new PayrollController();

try {
    $result = $apiController->{'undoImportToPayroll'}($reqBody["monthYear"]);
    // print_r($result);
    $result = $apiController->rollbackDeductCollection($reqBody["monthYear"]);
    // $result = $apiController->{'cancelPayrollTransaction'}($reqBody["monthYear"]);


} catch (Exception $e) {
    $result = $e;
}


if(!isset($result["error"])) {
    responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>