<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/LeaveController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
      // "username"=>Array("string", "required"),
      // "email"=>Array("string", "optional"),
      // "employeeId"=>Array("string", "optional"),
      // "password"=>Array("number", "optional"),
      // "company"=>Array("string", "optional"),

      // "condition"=>Array("array", "required", "key"=>Array(
      //   "recId"=>Array("string", "required"),
      // )),
      "company"=>Array("string", "required"),
      "condition"=>Array("object", "required"),

      "updateData"=>Array("array", "optional", "key"=>Array(
          // "employeeId"=>Array("string", "optional"),
          "type"=>Array("string", "optional"),
          "leaveReason"=>Array("string", "optional"),
          "issueDate" =>Array("string", "optional"),
          "startDate" =>Array("string", "optional"),
          "endDate" =>Array("string", "optional"),
          "totalDays" =>Array("number", "optional"),
          "approvedBy" =>Array("string", "optional"),
          "approveDate" =>Array("string", "optional"),
          "approveStatus" =>Array("string", "optional"),
          "approveNote" =>Array("string", "optional", "empty"),
          "remark" =>Array("string", "optional"),
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

$mapFileds = Array(
  "company"=>"company",
  "recId"=>"rec_id",
  // "employeeId"=>"employee_id",
  "type"=>"type",
  "leaveReason"=>"leave_reason",
  "issueDate" =>"issue_date",
  "startDate" =>"start_date",
  "endDate" =>"end_date",
  "approvedBy" =>"approved_by",
  "approveDate" =>"approve_date",
  "approveStatus" =>"approve_status",
  "approveNote" =>"approve_note",
);

$reqParams = Array();
$conditions = Array();

foreach ($reqBody["condition"] as $elCond) {
  $mappedEl = Array();

  foreach ($elCond as $key => $value) {
      if($mapFileds[$key]) {
          $mappedEl[$mapFileds[$key]] = $value;
      } else {
        $mappedEl[$key] = $value;
      }
  }
  if($mappedEl) {
      array_push($conditions, $mappedEl);
  }
}


$company = $reqBody["company"];
$apiController = new LeaveController();
$result = $apiController->{'updateLeave'}( $company, $conditions, $reqBody["updateData"]);

if(!isset($result["error"])) {
  responseSuccess($result ? 200:304, $result ? "Success" : "No data updated", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>