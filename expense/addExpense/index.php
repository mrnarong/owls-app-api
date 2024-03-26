<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/ExpenseController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);


$reqConfig = Array(
    "payload" => Array(
      "company"=>Array("string", "optional"),
      "expenseData"=>Array("object", "optional"),
    // "employeeId"=>Array("string", "required"),
      // "type"=>Array("string", "required"),
      // "leaveReason"=>Array("string", "required"),
      // "startDate"=>Array("string", "required"),
      // "endDate"=>Array("string", "required"),
      // "issueDate"=>Array("string", "required"),

    ),
    "headers" => Array(
      "Authorization"=>Array("string", "required"),
    )
);

// if(isset($_GET["doc"])) {
//   genApiDoc($reqConfig);
// }


$validation = new Validation;
$vdResult = $validation->checkHeader($reqConfig["headers"], getallheaders());
if($vdResult["message"]!=="Ok"){
    responseError(442, $vdResult["status"], $vdResult["message"], null);
}

$vdResult = $validation->checkPayload($reqConfig["payload"], $reqBody);
if($vdResult["message"]!=="Ok"){
    responseError(442, $vdResult["status"], $vdResult["message"], null);
}

// echo "addExpense Api";

$company = $reqBody["company"] ? $reqBody["company"] : getTokenCompany();
$apiController = new ExpenseController();
$result = $apiController->addExpense( $company, $reqBody );


if(!isset($result["error"])) {
  responseSuccess(200, "Success", $result);
} else {
    responseError(500, 500, "Internal Server Error", $result); //array('error' => $result["error"]));
}
?>