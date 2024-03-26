<?php
require __DIR__ . "/../../index.php";
// 
require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = Array(
    "payload" => Array(
        "fullname" => Array("string", "required"),
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

$tokens = explode(" ", strtolower($reqBody["fullname"]));
if(strlen($tokens[0]) <5) {
    $tokens = Array(str_replace(" ", "",  $reqBody["fullname"]));
} 

$fullname = substr($tokens[0], 0, 5+($tokens[1]?0:1));
if($tokens[1]) {
    $fullname .= (substr($tokens[1], 0, 1));
}

$apiController = new UserController();
$result = $apiController->getNewUsername($fullname);


if(!isset($result["error"])) {
    responseSuccess(200, "Success", Array("username"=>$result));
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>