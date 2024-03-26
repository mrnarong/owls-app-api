<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
require PROJECT_ROOT_PATH . "/controllers/api/EmployeeController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
        "company"=>Array("string", "required"),
        "condition"=>Array("array", "optional", "key"=>Array(
            "recId"=>Array("string", "optional"),
            "employeeId"=>Array("string", "optional"),
            "department"=>Array("string", "optional"),
            "status"=>Array("object", "optional"),
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


$mapSortFileds = Array(
    "recId"=>"rec_id",
    "company"=>"company",
    "employeeId"=>"employee_id",
    "department"=>"department",
    "status"=>"status",
  
  );
  $reqParams = Array();
  $conditions = Array();
  
//   print_r($reqBody["condition"]);
  if($reqBody["condition"]) {
    foreach ($reqBody["condition"] as $elCond) {
        $mappedEl = Array();
        foreach ($elCond as $key => $value) {
            if($mapSortFileds[$key]) {
                $mappedEl[$mapSortFileds[$key]] = $value;
            }
        }
        if($mappedEl) {
            array_push($conditions, $mappedEl);
        }
    }
  }

$company = $reqBody["company"];
$apiController = new EmployeeController();
// print_r($conditions);
$result = $apiController->getSalaryHist( $company, $conditions);

if(!isset($result["error"])) {
    responseSuccess($result ? 200:404, $result ? "Success" : "Data is not found", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>