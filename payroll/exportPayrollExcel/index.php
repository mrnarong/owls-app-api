<?php
require __DIR__ . "/../../index.php";
// 
require_once("../../lib/SimpleXLSXGen.php");
require PROJECT_ROOT_PATH . "/controllers/api/PayrollController.php";
require PROJECT_ROOT_PATH . "/controllers/api/ConfigController.php";

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
    "itemDate"=>"payroll_date",
    // "issueDate"=>"issue_date",
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
$controller = new PayrollController();
// $result = $apiController->{'getPayrolls'}($company, $reqParams);
$result = $controller->{'getEmployeePayrolls'}("", array("condition" => $conditions), $updateData);

$apiController = new ConfigController();
$confResult = $apiController->getConfig(Array("config_name"=>"api.config"), false);
$config = json_decode($confResult["config"], true);

// print_r($config["payroll"]);
// print_r($config["payroll"]["sso"]["max_income"]);
// print_r($config["payroll"]["wh_tax_rate"]);

$tax_rate = $config["payroll"]["wh_tax_rate"]; // 0.015;
$max_income = $config["payroll"]["sso"]["max_income"]; // 15000;
$min_income = $config["payroll"]["sso"]["min_income"]; // 1650;
$sso_rate = $config["payroll"]["sso"]["rate"]; // 0.05;
$max_amount = $config["payroll"]["sso"]["max_amount"]; // 750;

$sheets = Array();

$mapEmployee = array();
foreach ($result as $item) {
    if(!isset($mapEmployee[$item["company"]])) {
        $mapEmployee[$item["company"]] = Array();
    }
    if(!isset($mapEmployee[$item["company"]][$item["employeeId"]])) {
        $mapEmployee[$item["company"]][$item["employeeId"]] = Array(
            "employeeId"=>$item["employeeId"],
            "fullname"=>$item["fullname"],
            "idNumber"=>$item["idNumber"],
            "position"=>$item["position"],
            "department"=>$item["department"],
            "totalWhTax"=>$item["totalWhTax"],
            "totalSSO"=>$item["totalSSO"],
            // "salary"=>0,
            // "bonus"=>0,
            // "phoneAllowance"=>0,
            // "incentiveAmount"=>0,
            // "transportAmount"=>0,
            // "overAcheiveAmount"=>0,
            // "overtimeAmount"=>0,
            // "overtimeHours"=>0,
            // "totalIncome"=>0,
            // "sso"=>0.0,
            // "tax"=>0.0,

            "salary"=>0,
            "allowanceAmount"=>0,
            "incentiveAmount"=>0,
            "commissionAmount"=>0,
            "overAcheiveAmount"=>0,
            "overtimeAmount"=>0,
            "overtimeHours"=>0,
            "phoneAllowanceAmount"=>0,
            "transportAmount"=>0,
            "bonusAmount"=>0,
            "totalIncome"=>0,
            "totalDeduct"=>0,
            "netIncome"=>0,
            "sso"=>0.0,
            "tax"=>0.0,


        );
    }
    if($item["type"] == "ALLOWANCE") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["allowanceAmount"] = $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if($item["type"] == "SALARY") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["salary"] = $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if($item["type"] == "COMMISSION") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["commissionAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if($item["type"] == "JINCENTIVE") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["incentiveAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if($item["type"] == "OACHIEVED") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["overAcheiveAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if($item["type"] == "OVERTIME") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["overtimeAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["overtimeHours"] += ((float)$item["itemNote"]);
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if($item["type"] == "PCHARGE") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["phoneAllowanceAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if($item["type"] == "TRANSPORT") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["transportAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    if($item["type"] == "BONUS") {
        $mapEmployee[$item["company"]][$item["employeeId"]]["bonusAmount"] += $item["amount"];
        $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    }
    // if($item["type"] == "SALARY") {
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["salary"] = $item["amount"];
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    // }
    // if($item["type"] == "BONUS") {
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["bonus"] = $item["amount"];
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    // }
    // if($item["type"] == "PCHARGE") {
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["phoneAllowance"] = $item["amount"];
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    // }
    // if($item["type"] == "JINCENTIVE") {
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["incentiveAmount"] += $item["amount"];
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    // }
    // if($item["type"] == "TRANSPORT") {
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["transportAmount"] += $item["amount"];
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    // }
    // if($item["type"] == "OACHIEVED") {
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["overAcheiveAmount"] += $item["amount"];
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    // }
    
    // if($item["type"] == "OVERTIME") {
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["overtimeAmount"] += $item["amount"];
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["overtimeHours"] += ((float)$item["itemNote"]);
    //     $mapEmployee[$item["company"]][$item["employeeId"]]["totalIncome"] += $item["amount"];
    // }

    // $mapEmployee[$item["company"]][$item["employeeId"]] = 
}

foreach ($mapEmployee as $company => $employee) {
    // echo "Company: $company\n";
    // print_r($employee);
    // echo "\n====\n";
    foreach($employee as $employeeId => $item) {
        // print_r($mapEmployee[$company][$employeeId]);
        // echo "\n====".$mapEmployee[$company][$employeeId]["totalIncome"]." => ".$tax_rate."====\n";
            $mapEmployee[$company][$employeeId]["tax"] = $item["totalIncome"] * $tax_rate;
        if ($mapEmployee[$company][$employeeId]["totalIncome"] > $min_income && $mapEmployee[$company][$employeeId]["totalIncome"] <= $max_income) {
            $mapEmployee[$company][$employeeId]["sso"] = $mapEmployee[$company][$employeeId]["totalIncome"] * $sso_rate;
        } else if ($mapEmployee[$company][$employeeId]["totalIncome"] > $max_income) {
            $mapEmployee[$company][$employeeId]["sso"] = $max_amount;
        }
    }
  // $mapEmployee

}
$sheets = Array();
foreach ($mapEmployee as $company => $employee) {
    $sheets[$company] = Array(Array(
        "รหัสพนักงาน", "ชื่อ-นามสกุล", "เลขบัตร ปชช.", "ตำแหน่งงาน", "แผนก", 

        "เงินเดือน", "ค่าทำงานนอกสถานที่", "ค่าปิดจ๊อบ", "Overachieved Sales Target", 
        "ค่าโทรศัพท์", "ค่าเดินทาง", "เงินโบนัส",

        "ค่าล่วงเวลา (O.T.)", "Hours", 
        "รวมเงินได้", "ภาษีหัก ณ ที่จ่าย", "ประกันสังคม", "เงินได้สุทธิ", 
        "ภาษีสะสม", "ประกันสังคมสะสม"));
    foreach($employee as $employeeId => $item) {
        array_push($sheets[$company], Array(
            $item["employeeId"], $item["fullname"], $item["idNumber"], $item["position"], $item["department"], 
            
            $item["salary"], $item["allowanceAmount"], $item["incentiveAmount"],  $item["overAcheiveAmount"],
            $item["phoneAllowanceAmount"], $item["transportAmount"], $item["bonusAmount"],

            $item["overtimeAmount"], $item["overtimeHours"],
            
            $item["totalIncome"], $item["tax"], $item["sso"], $item["totalIncome"] - $item["tax"] - $item["sso"],
            $item["totalWhTax"] + $item["tax"], $item["totalSSO"] + $item["sso"], 
        ));
    }
}

$result = Array(
    "filename" => "Salary-".date("Ymd.Hisu").".xlsx",
    "sheets" => $sheets
);

if(!isset($result["error"])) {
    responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}
?>