<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
      // "username"=>Array("string", "required"),
      // "email"=>Array("string", "optional"),
      // "employeeId"=>Array("string", "optional"),
      // "password"=>Array("number", "optional"),
      // "company"=>Array("string", "optional"),

      "condition"=>Array("object", "required"),
      "updateData"=>Array("array", "optional", "key"=>Array(
          "username"=>Array("string", "optional"),
          "email"=>Array("string", "optional"),
          "employeeId"=>Array("string", "optional"),
          "password"=>Array("string", "optional"),
          "company"=>Array("string", "optional"),
          "role"=>Array("string", "optional"),
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
$apiController = new UserController();
$result = $apiController->updateUser( $reqBody["condition"], $reqBody["updateData"]);
// print_r($result);

if(!isset($result["error"])) {
  responseSuccess($result ? 200:304, $result ? "Success" : "No data updated", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>