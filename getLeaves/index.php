<?php
require __DIR__ . "/../index.php";
// 
require PROJECT_ROOT_PATH . "/controller/api/LeaveController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = Array(
    "payload" => Array(
    //   "username"=>Array("string", "optional"),
    //   "employeeId"=>Array("string", "optional"),
    //   "email"=>Array("string", "optional"),
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

$mapSortFileds = Array(
    "employeeId"=>"employee_id",
    "type"=>"type",
    "issueDate"=>"issue_date",
    "startDate"=>"start_date",
    "endDate"=>"end_date",
    "approveDate"=>"approve_date",
    "approveStatus"=>"approve_status",
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

$apiController = new LeaveController();
$result = $apiController->{'getLeaves'}($reqParams);


if($result["error"] == null) {
  responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>