<?php
require __DIR__ . "/../index.php";


require PROJECT_ROOT_PATH . "/controller/api/LeaveController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);


$reqConfig = Array(
    "payload" => Array(
      "employeeId"=>Array("string", "required"),
      "type"=>Array("string", "required"),
      "leaveReason"=>Array("string", "required"),
      "startDate"=>Array("string", "required"),
      "endDate"=>Array("string", "required"),
      "issueDate"=>Array("string", "required"),


      // "employee_id" => $reqParams["employeeId"],
      // "type" => $reqParams["type"],
      // "leave_reason" => $reqParams["leaveReason"],
      // "start_date" => $reqParams["startDate"],
      // "end_date" => $reqParams["endDate"],
      // "issue_date" => $reqParams["issueDate"],

    ),
    "headers" => Array(
      "Authorization"=>Array("string", "required"),
    )
);

if(isset($_GET["doc"])) {
  genApiDoc($reqConfig);
}


$validation = new Validation;
$vdResult = $validation->checkHeader($reqConfig["headers"], getallheaders());
if($vdResult["message"]!=="Ok"){
    responseError(442, $vdResult["status"], $vdResult["message"], null);
}

$vdResult = $validation->checkPayload($reqConfig["payload"], $reqBody);
if($vdResult["message"]!=="Ok"){
    responseError(442, $vdResult["status"], $vdResult["message"], null);
}
$apiController = new LeaveController();
$result = $apiController->{'addLeave'}( $reqBody);


if($result["error"] == null) {
    responseSuccess(200, "Success", $result);
} else {
    responseError(500, 500, "Internal Server Error", $result); //array('error' => $result["error"]));
}
?>