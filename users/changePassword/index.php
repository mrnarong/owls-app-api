<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
      "company"=>Array("string", "required"),
      "username"=>Array("string", "required"),
      "password"=>Array("string", "required"),
      "newPassword"=>Array("string", "required"),
    ),
    "headers" => Array(
      "Authorization"=>Array("string", "required"),
      // "ApiKey"=>Array("string", "required")
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
$result = $apiController->changePassword( $reqBody["company"], $reqBody["username"], $reqBody["password"], $reqBody["newPassword"]);
if(isset($result["code"])) {
  responseSuccess($result["code"], $result["message"]);
} else {
  responseError(500, 500, "Internal Server Error", array('error' => $result["error"]));
}

// if(!isset($result["error"])) {
//   responseSuccess($result ? 200:304, $result ? "Success" : "No data updated", $result);
// } else {
//     responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
// }
?>