<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/JobRouteController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
        "company"=>Array("string", "optional"),
        "jobRoute"=>Array("object", "optional"),

        // "employeeId"=>Array("string", "required"),

        // "routingDate"=>Array("string", "required"),
        // "originPlace"=>Array("string", "required"),
        // "originLat"=>Array("number", "required"),
        // "originLng"=>Array("number", "required"),

        // "destPlace"=>Array("string", "required"),
        // "destLat"=>Array("number", "required"),
        // "destLng"=>Array("number", "required"),

        // "distance"=>Array("number", "required"),
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
$company = $reqBody["company"] ? $reqBody["company"] : getTokenCompany();
$apiController = new JobRouteController();
$result = $apiController->addJobRoute( $company, $reqBody);


if(!isset($result["error"])) {
    responseSuccess(200, "Success", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>