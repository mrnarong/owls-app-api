<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/ConfigController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
      "configName"=>Array("string", "required"),
      "configItemsList"=>Array("object", "required"),
      "status"=>Array("boolean", "optional"),
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

// print_r($reqBody["status"]);
$controller = new ConfigController();
$result = $controller->getConfig(Array("config_name"=>$reqBody["configName"]), false);
// echo json_encode($reqBody["configItemsList"]);

if(sizeof($result)) {
  $newConfig = json_decode($result["config"], true);
  foreach($reqBody["configItemsList"] as $item) {
    foreach($item as $key => $value) {
      // echo $key . " " .$value;
      $newConfig[$key] = $value;
    }
  }

  // print_r($reqBody["configName"]);
  $updateData = Array("configData"=>$newConfig);
  if(isset($reqBody["status"])) {
    $updateData["status"] = $reqBody["status"] ? 1 : 0;
  }
  $result = $controller->updateConfig( $reqBody["configName"], $updateData );
  $result = Array(
    "code"=>200,
    "message"=>"Success"
  );
  
} else {
  $result = Array(
    "code"=>404,
    "message"=>"No config to update"
  );
}

if(!isset($result["error"])) {
  responseSuccess($result["code"], $result["message"], []);
} else {
  responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>