<?php
require __DIR__ . "/../../index.php";
// 
require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
require PROJECT_ROOT_PATH . "/controllers/api/EmployeeController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = Array(
    "payload" => Array(
      "company"=>Array("string", "optional"),
      "condition" => Array("object", "optional"),
      "sort"=>Array("array", "optional", "key"=>Array(
        "key"=>Array("string", "required"),
        "order"=>Array("string", "required"),
      )),
      // "username"=>Array("string", "optional"),
      // "employeeId"=>Array("string", "optional"),
    //   "companyId"=>Array("string", "required"),
    //   "fullname"=>Array("string", "required"),
    //   "mobileNo"=>Array("number", "required"),
    //   "token"=>Array("string", "required"),
    //   "role"=>Array("string", "required"),
    //   "status"=>Array("string", "required"),
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
  "username"=>"username",
  "userEmail"=>"email",
  "fullname"=>"fullname",
  "fullnameEn"=>"fullname_en",
  "employeeId"=>"employee_id",
  "idNumber"=>"id_number",
  "role"=>"role",

);
$reqParams = Array(
  // "username"=>$reqBody["username"],
  // "employee_id"=>$reqBody["employeeId"],
  // "user_email"=>$reqBody["email"],
);

$conditions = Array();

// print_r($reqBody["condition"]);
// $employeeId = "";
if($reqBody["condition"]) {
  // echo "Condition";
  foreach ($reqBody["condition"] as $elCond) {
      $mappedEl = Array();
      foreach ($elCond as $key => $value) {
          // if($key == "employeeId") {
          //   $employeeId = $value;
          // }
          if($mapSortFileds[$key]) {
              $mappedEl[$mapSortFileds[$key]] = $value;
          }
      }
      if($mappedEl) {
          array_push($conditions, $mappedEl);
      }
  }
}

// print_r($conditions);
$reqParams["condition"] = $conditions ? $conditions : [];

if($reqBody["sort"] && $mapSortFileds[$reqBody["sort"]["key"]] && (array_search(strtoupper($reqBody["sort"]["order"]), ["ASC", "DESC"])>-1)) {
  $reqParams["sort"]["key"] = $mapSortFileds[$reqBody["sort"]["key"]];
  $reqParams["sort"]["order"] = strtoupper($reqBody["sort"]["order"]);
}
// $company = $reqBody["company"] ? $reqBody["company"] : getTokenCompany();
$apiController = new EmployeeController();
$result = $apiController->getEmployee($reqBody["company"], $reqParams);


// print_r($reqBody["company"]);
// print_r($employeeId);

if(!isset($result["error"])) {
  responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>