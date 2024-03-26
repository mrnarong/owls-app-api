<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/OvertimeController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);


$reqConfig = Array(
    "payload" => Array(
      "company"=>Array("string", "optional"),
      "condition"=>Array("object", "required"),
      "updateData"=>Array("object", "required"),
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

$mapFields = Array(
  "recId"=>"rec_id",
  "employeeId"=>"employee _id",
  "startDate"=>"start_date",
  "endDate"=>"end_date",
  "project"=>"project",
  "detail"=>"detail",
  "status"=>"status",
  "approvedBy"=>"approved_by",
  "approveDate"=>"approve_date",
  "approveNote"=>"approval_note",
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


$company = $reqBody["company"] ? $reqBody["company"] : getTokenCompany();
if($reqBody["company"]) {
  array_push($conditions, Array("company"=>$reqBody["company"]));
}


// echo "Condition:\n";
// print_r($conditions);

// echo "\n\nupdateData:\n";
// print_r($updateData);

$controller = new OvertimeController();
$result = $controller->updateOvertime( $conditions, $updateData );


if(!isset($result["error"])) {
  responseSuccess($result?200:404, $result?"Success":"No data to update", $result);
} else {
    responseError(500, 500, "Internal Server Error", $result); //array('error' => $result["error"]));
}
?>