<?php
require __DIR__ . "/../../index.php";


require PROJECT_ROOT_PATH . "/controllers/api/PayrollController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);


$reqConfig = array(
  "payload" => array(
    "company" => array("string", "optional", "empty"),
    "condition" => array("object", "required"),
    "updateData" => array("object", "required"),
  ),
  "headers" => array(
    "Authorization" => array("string", "required"),
  )
);

$validation = new Validation;
$vdResult = $validation->checkHeader($reqConfig["headers"], getallheaders());
if ($vdResult["message"] !== "Ok") {
  responseError(442, $vdResult["status"], $vdResult["message"], null);
}

$vdResult = $validation->checkPayload($reqConfig["payload"], $reqBody);
if ($vdResult["message"] !== "Ok") {
  responseError(442, $vdResult["status"], $vdResult["message"], null);
}

$mapFields = array(
  "recId" => "rec_id",
  "employeeId" => "employee _id",
  "itemDate" => "payroll_date",
  "type" => "wage_type",
  "amount" => "amount",
  "taxRate" => "tax_rate",
  "payMethod" => "pay_method",
  "status" => "status",
  "approvedBy" => "approved_by",
  "approveDate" => "approve_date",
  "approvalNote" => "approval_note",
);

$conditions = array();

foreach ($reqBody["condition"] as $elCond) {
  $mappedEl = array();
  foreach ($elCond as $key => $value) {
    if ($mapFields[$key]) {
      $mappedEl[$mapFields[$key]] = $value;
    }
  }
  if ($mappedEl) {
    array_push($conditions, $mappedEl);
  }
}

$updateData = array();
foreach ($reqBody["updateData"] as $key => $value) {
  if ($mapFields[$key]) {
    $updateData[$mapFields[$key]] = $value;
  }
}


if ($reqBody["company"]) {
  array_push($conditions, array("company" => $reqBody["company"]));
}


// echo "Condition:\n";

// echo "\n\nupdateData:\n";
// print_r($updateData);

$controller = new PayrollController();
$result = $controller->updatePayroll($conditions, $updateData);
// print_r($result);

// $conditions[0]["status"] = "WAITING";
array_push($conditions, array("status" => "APPROVED"));

// print_r($conditions);

// $result = $controller->{'getEmployeePayrolls'}("", array("condition" => $conditions), $updateData);
$result = $controller->getPayrolls("", array("condition" => $conditions), $updateData);

$tax_rate = 0.015;
$max_income = 15000;
$min_income = 1650;
$sso_rate = 0.05;
$max_amount = 750;
$mapEmployee = array();
foreach ($result as $key => $item) {
  if (!isset($mapEmployee[$item["company"] . $item["employeeId"]])) {
    // $mapEmployee[$item["company"] . $item["employeeId"]]["items"] = array();
    $mapEmployee[$item["company"] . $item["employeeId"]]["company"] = $item["company"];
    $mapEmployee[$item["company"] . $item["employeeId"]]["employeeId"] = $item["employeeId"];
    // $mapEmployee[$item["company"] . $item["employeeId"]]["fullname"] = $item["fullname"];
    // $mapEmployee[$item["company"] . $item["employeeId"]]["idNumber"] = $item["idNumber"];
    // $mapEmployee[$item["company"] . $item["employeeId"]]["position"] = $item["position"];
    // $mapEmployee[$item["company"] . $item["employeeId"]]["department"] = $item["department"];
    $mapEmployee[$item["company"] . $item["employeeId"]]["sso"] = 0;
    $mapEmployee[$item["company"] . $item["employeeId"]]["tax"] = 0;
    $mapEmployee[$item["company"] . $item["employeeId"]]["totalIncome"] = 0;
    // $mapEmployee[$item["company"] . $item["employeeId"]]["salary"] = 0;
    // $mapEmployee[$item["company"] . $item["employeeId"]]["incentive"] = 0;
    // $mapEmployee[$item["company"] . $item["employeeId"]]["overtime_amount"] = 0;
    // $mapEmployee[$item["company"] . $item["employeeId"]]["overtime_hours"] = 0;
  }
  // print_r($item);
  // foreach($item as $key=>$data) {
    if($item["type"] == "SALARY") {
      // $mapEmployee[$item["company"] . $item["employeeId"]]["salary"] = $item["amount"];
      $mapEmployee[$item["company"] . $item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if($item["type"] == "JINCENTIVE") {
      // $mapEmployee[$item["company"] . $item["employeeId"]]["incentive"] = $item["amount"];
      $mapEmployee[$item["company"] . $item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if($item["type"] == "OVERTIME") {
      // $mapEmployee[$item["company"] . $item["employeeId"]]["overtime_amount"] += $item["amount"];
      // $mapEmployee[$item["company"] . $item["employeeId"]]["overtime_hours"] += ((float)$item["itemNote"]);
      $mapEmployee[$item["company"] . $item["employeeId"]]["totalIncome"] += $item["amount"];
    }

  // }
  // array_push($mapEmployee[$item["company"] . $item["employeeId"]]["items"], $item);
}
foreach ($mapEmployee as $key => $item) {
  $mapEmployee[$key]["tax"] = $mapEmployee[$key]["totalIncome"] * $tax_rate;
  // $mapEmployee[$key]["sso"] = $mapEmployee[$key]["totalIncome"] * $tax_rate;
  if ($mapEmployee[$key]["totalIncome"] > $min_income && $mapEmployee[$key]["totalIncome"] <= $max_income) {
    $mapEmployee[$key]["sso"] = $mapEmployee[$key]["totalIncome"] * $sso_rate;
  } else if ($mapEmployee[$key]["totalIncome"] > $max_income) {
    $mapEmployee[$key]["sso"] = $max_amount;
  }

  // $mapEmployee

}

// $excelData = "Company,Employee Id,Firstname Lastname,ID Number,Position,Department,Salary,Overtime,Incentive,Net Income,tax,sso";
$payrolls = Array();

// echo "Count: ".count($mapEmployee);
foreach ($mapEmployee as $key => $item) {
  // $payroll = Array(date("YmdHis"), $item["company"], date("Y-m-d H:i:s"), );
  // print_r($item);
  if(!isset($payrolls[$item["company"]])) {
    $payrolls[$item["company"]] = Array($item["employeeId"]=>Array());
  } else if(!isset($payrolls[$item["company"]][$item["employeeId"]])) {
    $payrolls[$item["company"]][$item["employeeId"]] = Array();
  }
  array_push($payrolls[$item["company"]][$item["employeeId"]], $item["totalIncome"]); //Array($item["totalIncome"], $item["tax"], $item["sso"]));
    // echo "$itemList\n";
        // $excelData .= ($item["company"] . "," . $item["employeeId"] . "," . $item["fullname"] . ",". $item["idNumber"] . ",". $item["position"] . "," . $item["department"] . ",". $item["salary"] . "," . $item["overtime_amount"] . "(" . $item["overtime_hours"] . " h)" . "," . $item["incentive"] . "," . $item["totalIncome"] . "," . $item["tax"] . "," . $item["sso"] . "\n");
}
// $payrollTransactions = Array();
// foreach($payrolls as $company=>$employees) {

//   foreach($employees as $employyId => $item) {

//   }
// }

$result = $controller->{'createPayrollTransaction'}($payrolls);


// $excelData .= $itemExcel;

$result = $controller->{'updateDeductCollection'}($mapEmployee);


$result = $payrolls;// array($excelData);


if (!isset($result["error"])) {
  responseSuccess(200, "Success", $result);
} else {
  responseError(500, 500, "Internal Server Error", $result); //array('error' => $result["error"]));
}
