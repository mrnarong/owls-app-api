<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
      "username"=>Array("string", "required"),
      "email"=>Array("string", "required"),
      "employeeId"=>Array("string", "required"),
      "password"=>Array("number", "required"),
      "company"=>Array("string", "required"),
      "role"=>Array("string", "required"),
    ),
    "headers" => Array(
      "Authorization"=>Array("string", "required"),
    //   "Apikey"=>Array("string", "required")
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
$apiController = new UserController();
$result = $apiController->{'addUser'}( $reqBody);


if(!isset($result["error"])) {
  responseSuccess(200, "Success", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>