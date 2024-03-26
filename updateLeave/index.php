<?php
require __DIR__ . "/../index.php";


require PROJECT_ROOT_PATH . "/controller/api/LeaveController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
      // "username"=>Array("string", "required"),
      // "email"=>Array("string", "optional"),
      // "employeeId"=>Array("string", "optional"),
      // "password"=>Array("number", "optional"),
      // "company"=>Array("string", "optional"),

      "condition"=>Array("array", "required", "key"=>Array(
        "recId"=>Array("string", "required"),
      )),
      "updateData"=>Array("array", "optional", "key"=>Array(
          "employeeId"=>Array("string", "optional"),
          "type"=>Array("string", "optional"),
          "leaveReason"=>Array("string", "optional"),
          "issueDate" =>Array("string", "optional"),
          "startDate" =>Array("string", "optional"),
          "endDate" =>Array("string", "optional"),
          "approveDate" =>Array("string", "optional"),
          "approveStatus" =>Array("string", "optional"),
          "approveReason" =>Array("string", "optional"),
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
$apiController = new LeaveController();
$result = $apiController->{'updateLeave'}( $reqBody["condition"], $reqBody["updateData"]);

if($result["error"] == null) {
  responseSuccess($result ? 200:304, $result ? "Success" : "No data updated", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>