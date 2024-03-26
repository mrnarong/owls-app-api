<?php
require __DIR__ . "/../../index.php";
// 
require PROJECT_ROOT_PATH . "/controllers/api/PayrollController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = Array(
    "payload" => Array(
    //   "username"=>Array("string", "optional"),
    //   "employeeId"=>Array("string", "optional"),
      "company"=>Array("string", "optional", "empty"),
        "condition" => Array("object", "optional"),

        "sort"=>Array("array", "optional", "key"=>Array(
            "key"=>Array("string", "required"),
            "order"=>Array("string", "required"),
        )),
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

$mapFileds = Array(
    "employeeId"=>"employee_id",
    "monthYear"=>"payroll_date",
    "type"=>"wage_type",
    "status"=>"status",
);
$reqParams = Array(
    // "username"=>$reqBody["username"],
    // "employee_id"=>$reqBody["employeeId"],
    // "user_email"=>$reqBody["email"],
);

$conditions = Array();
if($reqBody["condition"]) {
    // echo "Condition";
    foreach ($reqBody["condition"] as $elCond) {
        $mappedEl = Array();
        foreach ($elCond as $key => $value) {
            if($mapFileds[$key]) {
                $mappedEl[$mapFileds[$key]] = $value;
            }
        }
        if($mappedEl) {
            array_push($conditions, $mappedEl);
        }
    }
}

// print_r($conditions);
$reqParams["condition"] = $conditions ? $conditions : [];

if($reqBody["sort"] && $mapFileds[$reqBody["sort"]["key"]] && (array_search(strtoupper($reqBody["sort"]["order"]), ["ASC", "DESC"])>-1)) {
    $reqParams["sort"]["key"] = $mapFileds[$reqBody["sort"]["key"]];
    $reqParams["sort"]["order"] = strtoupper($reqBody["sort"]["order"]);
}

$company = $reqBody["company"];
$apiController = new PayrollController();
$result = $apiController->getPayrolls($company, $reqParams);


if(!isset($result["error"])) {
    responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>