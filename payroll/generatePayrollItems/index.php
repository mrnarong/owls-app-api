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

$result = Array();
try {
    $result = $apiController->importSalaryToPayroll($reqBody["monthYear"]);
 
    $result = $apiController->importCommissionToPayroll($reqBody["monthYear"]);

    $result = $apiController->importExpenseToPayroll($reqBody["monthYear"]);

    $result = $apiController->importIncentiveToPayroll($reqBody["monthYear"]);

    // $result = $apiController->importOverAcheiveToPayroll($reqBody["monthYear"]);

    $result = $apiController->importOvertimeToPayroll($reqBody["monthYear"]);

    $result = $apiController->importPhoneAllowanceToPayroll($reqBody["monthYear"]);

    $result = $apiController->importJobRouteToPayroll($reqBody["monthYear"]);

    // $result = $apiController->importBonusToPayroll($reqBody["monthYear"]);

} catch (Exception $e) {
    print_r($e);
    $result = $e;
}

if(!isset($result["error"])) {
    responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>