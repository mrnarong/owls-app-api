<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";
require PROJECT_ROOT_PATH . "/controllers/api/EmployeeController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
    //   "employeeId"=>Array("string", "required"),
    //   "username"=>Array("string", "optional"),
    //   "email"=>Array("string", "optional"),
    //   "fullname"=>Array("string", "optional"),
    //   "gender"=>Array("number", "optional"),
    //   "birthDate"=>Array("string", "optional"),
    //   "enrollDate"=>Array("string", "optional"),
    //   "contactNo"=>Array("string", "optional"),
    //   "contactPerson"=>Array("string", "optional"),
    //   "role"=>Array("string", "optional"),
    //   "department"=>Array("string", "optional"),
        "company"=>Array("string", "required"),
        "condition"=>Array("object", "required"),
            // "username"=>Array("string", "optional"),
            // "employee_id"=>Array("string", "optional"),

        "updateData"=>Array("array", "optional", "key"=>Array(
            // "employeeId"=>Array("string", "optional"),
            // "username"=>Array("string", "optional"),
            "fullname"=>Array("string", "optional"),
            "fullnameEn"=>Array("string", "optional"),
            "gender"=>Array("string", "optional"),
            "birthDate"=>Array("string", "optional"),
            "idNumber"=>Array("string", "optional"),
            "mobileNo"=>Array("string", "optional"),

            "enrollDate"=>Array("string", "optional"),
            "position"=>Array("string", "optional"),
            "department"=>Array("string", "optional"),
            "employType"=>Array("string", "optional"),

            "salary"=>Array("number", "optional"),
            "paymentType"=>Array("string", "optional"),
            "bank"=>Array("string", "optional"),
            "bankAccNo"=>Array("string", "optional"),

            "contactNo"=>Array("string", "optional"),
            "contactPerson"=>Array("string", "optional"),
            "relation"=>Array("string", "optional"),


            "documents"=>Array("string", "optional"),

            // address: "address",

            "email"=>Array("string", "optional"),
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

$mapFileds = Array(
    "recId"=>"rec_id",
    "company"=>"company",
    "employeeId"=>"employee_id",
    "username"=>"username",
    "email"=>"email",
    "fullname"=>"fullname",
    "gender"=>"gender",
    "birthdate"=>"birthdate",
    "enrollDate"=>"enroll_date",
    "contactNo"=>"contact_no",
    "contactPerson"=>"contact_person",
    "role"=>"role",
    "department"=>"department",
);
  $reqParams = Array();
  $conditions = Array();

  $employeeId = "";
  foreach ($reqBody["condition"] as $elCond) {
    $mappedEl = Array();

    foreach ($elCond as $key => $value) {
        if($key == "employeeId") {
            $employeeId = $value;
        }
        if($mapFileds[$key]) {
            $mappedEl[$mapFileds[$key]] = $value;
        } else {
          $mappedEl[$key] = $value;
        }
    }
    if($mappedEl) {
        array_push($conditions, $mappedEl);
    }
}


$company = $reqBody["company"];
$apiController = new EmployeeController();
// echo "Result  **** ";
// $result = $apiController->{'updateEmployee'}( $reqBody["condition"], $reqBody["updateData"]);
$result = $apiController->updateEmployee( $company, $conditions, $reqBody["updateData"]);
if(strlen($employeeId) && 
    (isset($reqBody["updateData"]["position"]) && strlen($reqBody["updateData"]["position"])) && 
    (isset($reqBody["updateData"]["department"])  && strlen($reqBody["updateData"]["department"])) &&
    (isset($reqBody["updateData"]["salary"]) && strlen($reqBody["updateData"]["salary"]))) {

        // print_r($employeeId);
        $result = $apiController->updateSalaryHist( $company, $employeeId, $reqBody["updateData"]["position"], $reqBody["updateData"]["department"],$reqBody["updateData"]["salary"]);
}

if($reqBody["updateData"]["email"]) {
    $userController = new UserController();
    $userData = Array(
        "email"=>$reqBody["updateData"]["email"],
        "company"=>$reqBody["updateData"]["company"]
    );
    // print_r($reqBody["condition"]);
    $userController->updateUser($reqBody["condition"], $userData);
}

if(!isset($result["error"])) {
    responseSuccess($result ? 200:304, $result ? "Success" : "No data updated", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>