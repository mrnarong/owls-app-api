<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/LeaveController.php";
require_once(PROJECT_ROOT_PATH . "/inc/utils.php");
$reqBody = json_decode(file_get_contents('php://input'), true);


$reqConfig = Array(
    "payload" => Array(
      "company"=>Array("string", "optional"),
      "leaveData"=>Array("object", "optional"),
    // "employeeId"=>Array("string", "required"),
      // "type"=>Array("string", "required"),
      // "leaveReason"=>Array("string", "required"),
      // "startDate"=>Array("string", "required"),
      // "endDate"=>Array("string", "required"),
      "fullname"=>Array("string", "optional"),

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
$company = $reqBody["company"] ? $reqBody["company"] : getTokenCompany();
$apiController = new LeaveController();
$result = $apiController->addLeave( $company, $reqBody );

// print_r($result);

if($result > -1) {
  $token = base64_encode(API_KEY);
  $to = LEAVE_APPROVER_MAIL;
  $baseUrl = CLIENT_WEB_URL;

  $url = $baseUrl."/admin/approve?token=$token&refNo=$result&company=".$reqBody["company"]."&employeeId=".$reqBody["leaveData"]["employeeId"]."&type=leave&by=$to&action=";
  $approved = $url."yes";
  $denied   = $url."no";

  $body = "<h3>มีใบลาขอการอนุมัติจากท่าน รายละเอียดดังนี้</h3><br>
  <table>
  <tr><td><b>บริษัท</b></td><td>".$reqBody["company"]."</td></tr>
  <tr><td><b>รหัสพนักงาน</b></td><td>".$reqBody["leaveData"]["employeeId"]."</td></tr>
  <tr><td><b>ชื่อ-นามสกุล</b></td><td>".$reqBody["leaveData"]["fullname"]."</td></tr>
  <tr><td><b>ประเภทการลา</b></td><td>".$reqBody["leaveData"]["type"]."</td></tr>
  <tr><td><b>วันที่ลา</b></td><td>".$reqBody["leaveData"]["startDate"]." - ".$reqBody["leaveData"]["endDate"]."</td></tr>
  <tr><td><b>จำนวนวันที่ลา</b></td><td>".$reqBody["leaveData"]["totalDays"]."</td></tr>
  <tr><td><b>เหตุผลการลา</b></td><td>".$reqBody["leaveData"]["leaveReason"]."</td></tr>
  </table><br>
  
  <a href=\"$approved\">[ อนุมัติ ]</a>&nbsp;&nbsp;&nbsp;
  <a href=\"$denied\">[ ไม่อนุมัติ ]</a>
  
  ";
  $util = new Utils();
  $result = $util->sendMail("", $to, "ใบลารอการอนุมัติ", $body);
}

if(!isset($result["error"])) {
  responseSuccess(200, "Success", $result);
} else {
    responseError(500, 500, "Internal Server Error", $result); //array('error' => $result["error"]));
}
?>