<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
      "company"=>Array("string", "required"),
      "employeeId"=>Array("string", "required"),
      "username"=>Array("string", "required"),
      "token"=>Array("string", "required"),
      "email"=>Array("string", "required"),
      "password"=>Array("string", "required"),
    ),
    "headers" => Array(
      // "Authorization"=>Array("string", "required"),
      "Apikey"=>Array("string", "required")
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
$controller = new UserController();
$result = $controller->setActivateStatus( Array( 
  "token"=>$reqBody["token"],
  "email"=>$reqBody["email"]
));

// print_r($result);
if($result) {
  $result = $controller->updateUser(Array(Array(
    "company"=> $reqBody["token"],
    "employee_id"=> $reqBody["employeeId"],
    "username"=> $reqBody["username"],
    "user_email"=> $reqBody["email"],
  )), Array("password"=>$reqBody["password"]));
}

// if(isset($result["code"])) {
//   responseSuccess($result["code"], $result["message"]);
// } else {
//   responseError(500, 500, "Internal Server Error", array('error' => $result["error"]));
// }

if(!isset($result["error"])) {
  responseSuccess($result ? 200:404, $result ? "Success" : "Data not found!", $result);
  // header("Location: http://localhost:3000/login", true, 0); 
  // exit();
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>