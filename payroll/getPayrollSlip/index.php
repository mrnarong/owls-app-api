<?php
require __DIR__ . "/../../index.php";
require_once("payroll.inc.php");

// 
require PROJECT_ROOT_PATH . "/controllers/api/PayrollController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = array(
    "payload" => array(
        //   "username"=>Array("string", "optional"),
        //   "employeeId"=>Array("string", "optional"),
        "company" => array("string", "required"),
        "condition" => array("object", "optional"),

        "sort" => array("array", "optional", "key" => array(
            "key" => array("string", "required"),
            "order" => array("string", "required"),
        )),
        //   "companyId"=>Array("string", "required"),
        //   "fullname"=>Array("string", "required"),
        //   "mobileNo"=>Array("number", "required"),
        //   "token"=>Array("string", "required"),
        //   "role"=>Array("string", "required"),
        //   "status"=>Array("string", "required"),
    ),
    "headers" => array(
        "Authorization" => array("string", "required"),
        //   "ApiKey"=>Array("string", "required")
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

$mapFileds = array(
    "employeeId" => "employee_id",
    "monthYear" => "payroll_date",
    // "issueDate"=>"issue_date",
    "status" => "status",
);
$reqParams = array(
    // "username"=>$reqBody["username"],
    // "employee_id"=>$reqBody["employeeId"],
    // "user_email"=>$reqBody["email"],
);

$employeeId = "";
$monthYear = "";
// $conditions = Array();
// if($reqBody["condition"]) {
//     // echo "Condition";
//     foreach ($reqBody["condition"] as $elCond) {
//         $mappedEl = Array();
//         foreach ($elCond as $key => $value) {
//             if($mapFileds[$key]) {
//                 $mappedEl[$mapFileds[$key]] = $value;
//             }
//             if($key == "employeeId") {
//                 $employeeId = $value;
//             }
//             if($key == "monthYear") {
//                 $monthYear = str_replace("-", "", $value);
//             }
//         }
//         if($mappedEl) {
//             array_push($conditions, $mappedEl);
//         }
//     }
// }

$conditions = array();
if ($reqBody["condition"]) {
    // echo "Condition";
    foreach ($reqBody["condition"] as $elCond) {
        $mappedEl = array();
        foreach ($elCond as $key => $value) {
            if ($mapFileds[$key]) {
                $mappedEl[$mapFileds[$key]] = $value;
            }
        }
        if ($mappedEl) {
            array_push($conditions, $mappedEl);
        }
    }
}

// print_r($conditions);
// $reqParams["condition"] = $conditions ? $conditions : [];

if ($reqBody["sort"] && $mapFileds[$reqBody["sort"]["key"]] && (array_search(strtoupper($reqBody["sort"]["order"]), ["ASC", "DESC"]) > -1)) {
    $reqParams["sort"]["key"] = $mapFileds[$reqBody["sort"]["key"]];
    $reqParams["sort"]["order"] = strtoupper($reqBody["sort"]["order"]);
}

$company = $reqBody["company"];
$apiController = new PayrollController();
$result = $apiController->getEmployeePayrolls($company, array("condition" => $conditions));
// print_r($conditions);
$config = getConfig("api.config");
$mapCompany = array();
foreach ($config["companies"] as $item) {
    $mapCompany[$item["key"]] = $item;
}
$mapLeaves = array();
foreach ($config["leaves"]["leaveTypes"] as $item) {
    $mapLeaves[$item["key"]] = $item;
}
// print_r($mapLeaves);

$mapDepartments = array();
foreach ($config["empDepts"] as $item) {
    $mapDepartments[$item["key"]] = $item;
}

// print_r($config["taxConfig"]["whTax"]);

$tax_rate = $config["taxConfig"]["whTax"];

$max_income = $config["ssoConfig"]["maxIncome"];
$min_income = $config["ssoConfig"]["minIncome"];
$sso_rate = $config["ssoConfig"]["rate"];
$max_amount = $config["ssoConfig"]["maxAmount"];

$mapEmployee = array();
foreach ($result as $item) {
    if (!isset($mapEmployee[$item["company"]])) {
        $mapEmployee[$item["company"]] = array();
    }
    if (!isset($mapEmployee[$item["company"]][$item["employeeId"]])) {
        $mapEmployee[$item["company"]][$item["employeeId"]] = array(
            "payrollDate" => substr($item["itemDate"], 0, 7) . "-01",
            "employeeId" => $item["employeeId"],
            "fullname" => $item["fullname"],
            "idNumber" => $item["idNumber"],
            "position" => $item["position"],
            "department" => $mapDepartments[$item["department"]]["value"],
            "totalWhTax" => $item["totalWhTax"],
            "totalSSO" => $item["totalSSO"],
            // "leaveType"=>$item["leave_type"],
            // "totalDays"=>$item["total_days"],
            "salary" => 0,
            "allowanceAmount" => 0,
            "incentiveAmount" => 0,
            "commissionAmount" => 0,
            "overAcheiveAmount" => 0,
            "overtimeAmount" => 0,
            "overtimeHours" => 0,
            "phoneAllowanceAmount" => 0,
            "transportAmount" => 0,
            "bonusAmount" => 0,
            "totalIncome" => 0,
            "totalDeduct" => 0,
            "netIncome" => 0,
            "sso" => 0.0,
            "tax" => 0.0,
            "status" => $item["status"],

        );
    }
    if ($item["type"] == "ALLOWANCE") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["allowanceAmount"] = $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if ($item["type"] == "EXPENSE") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["expenseAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if ($item["type"] == "SALARY") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["salary"] = $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if ($item["type"] == "JINCENTIVE") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["incentiveAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if ($item["type"] == "COMMISSION") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["commissionAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if ($item["type"] == "OACHIEVED") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["overAcheiveAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if ($item["type"] == "OVERTIME") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["overtimeAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["overtimeHours"] += ((float)$item["itemNote"]);
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if ($item["type"] == "PCHARGE") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["phoneAllowanceAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if ($item["type"] == "TRANSPORT") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["transportAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if ($item["type"] == "BONUS") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["bonusAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }

    // $mapEmployee[$item["company"]][$item["employeeId"]] = 
}

// $companyInfo = null;
// // if($config["companies"]) {
//     foreach($config["companies"] as $item) {
//         print_r($item);
//         if($item["key"] === $company) {
//             $companyInfo = $item;
//             break;
//         }
//     }
// // }
// print_r($mapEmployee);
// echo sizeof($mapEmployee);

// print_r($conditions);
if (sizeof($mapEmployee)) {
    $pdf = new PDFPayrollSlip();
    $leaveModel = new LeaveModel();
    $numSlip = 0;
    $fullname = "";
    // $payrollDate = "";

    $payrollDate = ""; //$conditions;
    // print_r($reqBody["condition"]);
    foreach ($reqBody["condition"] as $cond) {
        foreach($cond as $key=>$value){
            // echo $key."=>".$value."\n";
            if($key === "monthYear") {
                $value = str_replace("^", "", $value["\$regex"]);
                $dt = explode("-", $value);
                $lastDate = cal_days_in_month(CAL_GREGORIAN, $dt[1], $dt[0]);
                $payrollDate = sprintf("%s-%02d", $value, $lastDate);
            }
        }
    }
    // echo $payrollDate;

    foreach ($mapEmployee as $company => $employee) {
        foreach ($employee as $employeeId => $item) {
            $numSlip++;
            // $mapEmployee[$company][$employeeId]["tax"] = $item["totalIncome"] * $tax_rate;
            // if ($mapEmployee[$company][$employeeId]["totalIncome"] > $min_income && $mapEmployee[$company][$employeeId]["totalIncome"] <= $max_income) {
            //     $mapEmployee[$company][$employeeId]["sso"] = $mapEmployee[$company][$employeeId]["totalIncome"] * $sso_rate;
            // } else if ($mapEmployee[$company][$employeeId]["totalIncome"] > $max_income) {
            //     $mapEmployee[$company][$employeeId]["sso"] = $max_amount;
            // }
            $item["payrollDate"] = $payrollDate;
            $item["tax"] = $item["totalIncome"] * $tax_rate;
            if ($item["totalIncome"] > $min_income && $item["totalIncome"] <= $max_income) {
                $item["sso"] = $item["totalIncome"] * $sso_rate;
            } else if ($item["totalIncome"] > $max_income) {
                $item["sso"] = $max_amount;
            }
            $item["companyInfo"] = $mapCompany[$company];
            $item["leaveConfig"] = $mapLeaves;

            $leaveSummary = $leaveModel->getLeaveSummary($company, array("condition" => $conditions), array());
            // print_r($leaveSummary);
            $item["leaves"] = array();
            foreach ($leaveSummary as $leaveItem) {
                $item["leaves"][$leaveItem["type"]] = $leaveItem;
            }
            $item["totalDeduct"] += $item["sso"];
            $item["totalDeduct"] += $item["tax"];

            $item["netIncome"] += $item["totalIncome"];
            $item["netIncome"] -= $item["totalDeduct"];
            $fullname = $item["fullname"];
            $pdf->addPage($item, true);
        }
        // $mapEmployee
    }

    // $result =  $mapEmployee;
    // $pdf->addPage("", true);
    // $pdf->addPage("", false);
    $filename = $numSlip > 1 ? "all_" . date("Ym") : ($employeeId . "_" . str_replace(" ", "-", $fullname) . "_" . str_replace("-", "", substr($payrollDate, 0, 7)));
    $result = array(
        array(
            "base64" => $pdf->render(), //renderPayrollForm(),
            "filename" => "payroll-slips_" . $filename . ".pdf"
        )
    );




    if (!isset($result["error"])) {
        responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
    } else {
        responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
    }
} else {
    responseSuccess(404, "Not found", []);
}
