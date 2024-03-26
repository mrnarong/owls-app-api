<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/JobRouteController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = Array(
    "payload" => Array(
      "company"=>Array("string", "required"),
      // "condition"=>Array("array", "required", "key"=>Array(
      //   "recId"=>Array("number", "required"),
      //   // "employeeId"=>Array("string", "optional"),
      // )),
      "condition"=>Array("object", "required"),

      "updateData"=>Array("array", "optional", "key"=>Array(
          "employeeId"=>Array("string", "optional"),
          "routingDate"=>Array("string", "optional"),
          "originPlace"=>Array("string", "optional"),
          "originLat" =>Array("number", "optional"),
          "originLng" =>Array("number", "optional"),
          "destPlace" =>Array("string", "optional"),
          "destLat" =>Array("number", "optional"),
          "destLng" =>Array("number", "optional"),
          "distance" =>Array("number", "optional"),
          "approvedBy" =>Array("string", "optional"),
          "approveDate" =>Array("string", "optional"),
          "status" =>Array("string", "optional"),
          "approveNote" =>Array("string", "optional", "empty"),
          "remark" =>Array("string", "optional"),
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
  "employeeId"=>"employee_id",
  "routingDate"=>"routing_date",
  "status"=>"status",
  "employeeId"=>"employee_id",
  "routingDate"=>"routing_date",
  "originPlace"=>"origin_place",
  "originLat" =>"origin_lat",
  "originLng" =>"origin_lng",
  "destPlace" =>"dest_place",
  "destLat" =>"dest_lat",
  "destLng" =>"dest_lng",
  "distance" =>"distance",
);
$reqParams = Array(
  // "username"=>$reqBody["username"],
  // "employee_id"=>$reqBody["employeeId"],
  // "user_email"=>$reqBody["email"],
);

$conditions = Array();
  // echo "Condition";

//   "condition": [
//     {"recId": 1}
// ],

foreach ($reqBody["condition"] as $elCond) {
    $mappedEl = Array();

    // $orCond = Array();
    foreach ($elCond as $key => $value) {
        if($mapSortFileds[$key]) {
            // $mappedEl[$mapSortFileds[$key]] = $value;
            $mappedEl[$mapSortFileds[$key]] = $value;
        } else {
          $mappedEl[$key] = $value;
        }
    }
    if($mappedEl) {
        array_push($conditions, $mappedEl);
    }
}


// echo "condition\n";
// print_r($conditions);
// echo "\ncondition\n";


// $reqBody["condition"] = $conditions ? $conditions : [];




$company = $reqBody["company"];
$apiController = new JobRouteController();
$result = $apiController->updateJobRoute($company, $conditions, $reqBody["updateData"]);

if(!isset($result["error"])) {
  responseSuccess($result ? 200:304, $result ? "Success" : "No data updated", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>