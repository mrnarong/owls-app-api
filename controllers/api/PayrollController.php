<?php
class PayrollController extends BaseController
{

    
    public function getPayrolls($company, $reqParams, $limit=0){
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
            $responseData = $model->getPayrolls($company, $reqParams, $limit);
            // print_r($responseData);

        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
            return array_map(function($item) {
                return Array(
                    "recId" => $item["rec_id"],
                    "refNo" => $item["ref_no"],
                    "company" => $item["company"],
                    "employeeId" => $item["employee_id"],
                    "fullname" => $item["fullname"],
                    "itemDate" => $item["payroll_date"],
                    "itemNote" => $item["item_note"],
                    "type" => $item["wage_type"],
                    "amount" => $item["amount"],
                    "status" => $item["status"],
                );
            
            }, $responseData);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function getEmployeePayrolls($company, $reqParams, $limit=0){
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
            $responseData = $model->getEmployeePayrolls($company, $reqParams, $limit);
            // print_r($responseData);

        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
            return array_map(function($item) {
                return Array(
                    "recId" => $item["rec_id"],
                    // "refNo" => $item["ref_no"],
                    "company" => $item["company"],
                    "employeeId" => $item["employee_id"],
                    "fullname" => $item["fullname"],
                    "idNumber" => $item["id_number"],
                    "position" => $item["position"],
                    "department" => $item["department"],
                    "itemDate" => $item["payroll_date"],
                    "itemNote" => $item["item_note"],
                    "type" => $item["wage_type"],
                    "amount" => $item["amount"],
                    "status" => $item["status"],
                    "totalWhTax" => $item["total_wh_amount"],
                    "totalSSO" => $item["total_sso_amount"],
                );
            
            }, $responseData);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function getPayrollSlip($company, $reqParams, $limit=0){
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
            $responseData = $model->getPayrollSlip($company, $reqParams, $limit);
            // print_r($responseData);

        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
            return array_map(function($item) {
                return Array(
                    // "recId" => $item["rec_id"],
                    "company" => $item["company"],
                    "employeeId" => $item["employee_id"],
                    "payRollDate" => $item["payroll_date"],
                    "type" => $item["wage_type"],
                    "amount" => $item["amount"],
                    "status" => $item["status"],
                );
            
            }, $responseData);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function updatePayroll($condition, $updateData){
        $strErrorDesc = '';

        try{
            $model = new PayrollModel();
            $responseData = $model->updatePayroll($condition, $updateData);
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return $responseData;
    }


    public function getPayrollSummary($company, $reqParams, $limit=0){
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
            $responseData = $model->getPayrollSlip($company, $reqParams, $limit);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
            return array_map(function($item) {
                return Array(
                    "monthYear" => $item["month_year"],
                    "type" => $item["type"],
                    "status" => $item["status"],
                    "count" => $item["count"],
                );
            }, $responseData);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function importSalaryToPayroll($monthYear) {
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
            $responseData = $model->importSalaryToPayroll($monthYear);
            // print_r($responseData); 
            return $responseData;
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    
    public function importExpenseToPayroll($monthYear) {
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
            $responseData = $model->importExpenseToPayroll($monthYear);
            return $responseData;
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function importJobRouteToPayroll($monthYear) {
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
            $responseData = $model->importJobRouteToPayroll($monthYear);
            return $responseData;
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    

    
    public function importCommissionToPayroll($monthYear) {
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
                // echo "Entering importExpenseToPayroll ";

            $responseData = $model->importCommissionToPayroll($monthYear);
            return $responseData;
        } catch (Error $e) {
            print_r($e);
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }
    
    public function importIncentiveToPayroll($monthYear) {
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
                // echo "Entering importExpenseToPayroll ";

            $responseData = $model->importCommissionToPayroll($monthYear);
            return $responseData;
        } catch (Error $e) {
            print_r($e);
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function importPhoneAllowanceToPayroll($monthYear) {
        $strErrorDesc = '';
        try {
            $configModel = new ConfigModel();
            $confResult = $configModel->getConfig(Array("config_name"=>"common"), false);
            $config = json_decode($confResult[0]["data"], true);
            // print_r($config["expenses"]);
            $pChargeValue = 0;
            foreach($config["expenses"] as $expense) {
                if($expense["key"] == "PCHARGE") {
                    $pChargeValue = $expense["value"];
                    break;
                }
            }
            $model = new PayrollModel();
            $salaryResult = $model->getParollItems("SALARY", $monthYear);
            // print_r($salaryResult);
            // echo "\nsalaryResult\n";
            // print_r($salaryResult);
            // echo "\nsalaryResult\n";
    

            $responseData = $model->importPhoneAllowanceToPayroll($monthYear, $salaryResult, $pChargeValue);
            return $responseData;
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    
    public function importOvertimeToPayroll($monthYear) {
        $strErrorDesc = '';
        $mapSalary = Array();
        try {
            $model = new PayrollModel();
            $salary = $model->getActiveSalaryList();
            foreach($salary as $item) {
                $mapSalary[$item["company"]."_".$item["employee_id"]] = $item["salary"];
            }
            // $responseData = $model->importOvertimeToPayroll($monthYear);

            $overtimes = $model->getEmployeeOvertime($monthYear);
            $insertData = "";
            foreach($overtimes as $item) {
                $insertData .= ('("'.$item["company"].'", "'.$item["employee_id"].'", "OVERTIME", '.($item["hours"]*1.5*($mapSalary[$item["company"]."_".$item["employee_id"]]/30/8)).', "'.$item["start_datetime"].'", 0, "WAITING", '.$item["rec_id"].', "'.$item["hours"].'"),');
            }
            $insertData = rtrim($insertData, ',');

            // echo "\n---->";
            // print_r($insertData);
            // echo "<----\n";
            if($insertData) {
                $result = $model->importOvertimeToPayroll($insertData);
            } else {
                $result = 1;
            }
            return $result;
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }
    
    public function updateDeductCollection($data) {
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
            $responseData = $model->updateDeductCollection($data);
            return $responseData;
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function createPayrollTransaction($data) {
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
            $responseData = $model->createPayrollTransaction($data);
            return $responseData;
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }


    public function rollbackDeductCollection($monthYear) {
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
            $responseData = $model->rollbackDeductCollection($monthYear);
            return $responseData;
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function undoImportToPayroll($monthYear) {
        $strErrorDesc = '';
        try {
            $model = new PayrollModel();
            $responseData = $model->undoImportToPayroll($monthYear);
            // print_r($responseData);
            return $responseData;
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function addPayrollAdditional($company, $reqParams){
        $strErrorDesc = '';
        // $arrQueryStringParams = $this->getQueryStringParams();
        try {
            $model = new PayrollModel();
            $responseData = $model->addPayrollAdditional($company, 
                    Array(
                        "employee_id" => $reqParams["payrollData"]["employeeId"],
                        "payroll_date" => $reqParams["payrollData"]["itemDate"],
                        "type" => $reqParams["payrollData"]["type"],
                        "amount" => $reqParams["payrollData"]["amount"],
                        "status" => $reqParams["payrollData"]["status"],
                    )
            );
        } catch (Error $e) {
            print_r($e);
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return true;
    }
}
