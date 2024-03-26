<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/PayrollController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);


$reqConfig = Array(
    "payload" => Array(
      "company"=>Array("string", "optional", "empty"),
      "condition"=>Array("object", "required"),
      "updateData"=>Array("object", "required"),
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

$mapFields = Array(
  "recId"=>"rec_id",
  "employeeId"=>"employee _id",
  "itemDate"=>"payroll_date",
  "type"=>"wage_type",
  "amount"=>"amount",
  "taxRate"=>"tax_rate",
  "payMethod"=>"pay_method",
  "status"=>"status",
  "approvedBy"=>"approved_by",
  "approveDate"=>"approve_date",
  "approvalNote"=>"approval_note",
);

$conditions = Array();

foreach ($reqBody["condition"] as $elCond) {
    $mappedEl = Array();
    foreach ($elCond as $key => $value) {
        if($mapFields[$key]) {
            $mappedEl[$mapFields[$key]] = $value;
        }
    }
    if($mappedEl) {
        array_push($conditions, $mappedEl);
    }
}

$updateData = Array();
foreach ($reqBody["updateData"] as $key => $value) {
      if($mapFields[$key]) {
          $updateData[$mapFields[$key]] = $value;
      }
}


if($reqBody["company"]) {
  array_push($conditions, Array("company"=>$reqBody["company"]));
}


// echo "Condition:\n";
// print_r($conditions);

// echo "\n\nupdateData:\n";
// print_r($updateData);

$controller = new PayrollController();
$result = $controller->{'updatePayroll'}( $conditions, $updateData );


if(!isset($result["error"])) {
  responseSuccess(200, "Success", $result);
} else {
    responseError(500, 500, "Internal Server Error", $result); //array('error' => $result["error"]));
}
?>