<?php
require __DIR__ . "/../../index.php";
// 
require PROJECT_ROOT_PATH . "/controllers/api/ExpenseController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = Array(
    "payload" => Array(
        "company"=>Array("string", "optional", "empty"),
        "condition" => Array("object", "required"),
        "sort"=>Array("array", "optional", "key"=>Array(
            "key"=>Array("string", "required"),
            "order"=>Array("string", "required"),
        )),
    ),
    "headers" => Array(
      "Authorization"=>Array("string", "required"),
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
    // "itemDate"=>"item_date",
    "monthYear"=>"item_date",
    "approveDate"=>"approve_date",
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

$company = $reqBody["company"];// ? $reqBody["company"] : getTokenCompany();
$apiController = new ExpenseController();
// echo "getExpenses api";
// echo $reqParams;

$result = $apiController->{'getExpenses'}($company, $reqParams);
// $result = Array(
//     Array(
//         "no"=>1,
//         "date"=>"1 ต.ค. 2566",
//         "detail"=> "ค่าชุดตรวจโควิด",
//         "amount"=>200.0,
//         "status"=>"waiting"
//     ),
//     Array(
//         "no"=>2,
//         "date"=>"2 ต.ค. 2566",
//         "detail"=> "ค่าทางด่วน",
//         "amount"=>1500.0,
//         "status"=>"denied"
//     ),
//     Array(
//         "no"=>3,
//         "date"=>"3 ต.ค. 2566",
//         "detail"=> "ค่าปากกาเคมี",
//         "amount"=>50.0,
//         "status"=>"approved"
//     ),
// );


if(!isset($result["error"])) {
    responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>