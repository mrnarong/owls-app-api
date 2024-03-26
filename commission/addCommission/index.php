<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/CommissionController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);


$reqConfig = Array(
    "payload" => Array(
      "company"=>Array("string", "optional"),
      "commData"=>Array("object", "optional"),
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
$company = $reqBody["company"] ? $reqBody["company"] : getTokenCompany();
$controller = new CommissionController();
$result = $controller->{'addCommission'}( $company, Array(
    // "company" => $company,
    "employee_id" => $reqBody["commData"]["employeeId"],
    "issue_date" => $reqBody["commData"]["itemDate"],
    "role" => $reqBody["commData"]["role"],
    "type" => $reqBody["commData"]["commType"],
    "amount" => $reqBody["commData"]["amount"],
    "project" => $reqBody["commData"]["project"],
    "project_value" => $reqBody["commData"]["projectValue"],
) );


if(!isset($result["error"])) {
  responseSuccess(200, "Success", $result);
} else {
    responseError(500, 500, "Internal Server Error", $result); //array('error' => $result["error"]));
}
?>