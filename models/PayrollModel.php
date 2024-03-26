<?php
require_once PROJECT_ROOT_PATH . "/models/Database.php";
class PayrollModel extends Database
{

   

    public function addPayroll($company, $reqParams)
    {
        // $query = "INSERT INTO employee_leaves ".
        // "(company, employee_id, type, leave_reason, start_date, end_date".( isset($reqParams["issue_date"]) ? ", issue_date" : "").")".
        // "VALUES (?, ?, ?, ?, ?, ?".(isset($reqParams["issue_date"]) ? ", ?":"").")";

        // $addParams = Array("ssssss".(isset($reqParams["issue_date"]) ? "s":""));
        // $addParams = array_merge($addParams, Array($company, $reqParams["employee_id"], $reqParams["type"], $reqParams["leave_reason"],$reqParams["start_date"], $reqParams["end_date"]));
        // if(isset($reqParams["issue_date"])){
        //     array_push($addParams, $reqParams["issue_date"]);
        // }

        // print_r($addParams);

        $query = "INSERT INTO payroll_items " .
            "(company, employee_id, type, leave_reason, start_date, end_date)" .
            "VALUES (?, ?, ?, ?, ?, ?)";


        return $this->insert($query, array(
            "ssssss",
            $company, $reqParams["employee_id"], $reqParams["type"], $reqParams["leave_reason"], $reqParams["start_date"], $reqParams["end_date"]
        ));
    }

    //  SELECT employee_leaves.*, employees.fullname FROM employee_leaves 
    //  LEFT JOIN employees ON employee_leaves.employee_id=employees.employee_id 
    //  WHERE employee_leaves.employee_id='OWL0001' employee_leaves.employee_id=?
    //  ORDER BY employee_leaves.issue_date

    public function getPayrolls($company, $reqParams, $limit = 0)
    {
        if ($company) {
            array_push($reqParams["condition"], array("company" => $company));
        }
        $mapFileType = array(
            "t1.company" => array("s", "upper"),
            // "username"=>Array("s", "upper"),
            "t1.wage_type" => array("s", "upper"),
            "t1.employee_id" => array("s", "upper"),
            "t1.payroll_date" => array("s", "regex"),
            "t1.status" => array("s", "upper"),
        );

        $condition = Array();
        foreach($reqParams["condition"] as $group){
            $newGroup = Array();
            foreach($group as $key=>$value){
                // echo "\n".$value."\n";
               $newGroup["t1.".$key] = $value;
            }
            array_push($condition, $newGroup);
        }

        $sort = isset($reqParams["sort"]) ? $reqParams["sort"] : "";
        // print_r($reqParams);
        $queryOptions = getQueryOptions($mapFileType, $condition);

        // print_r($queryOptions);
        $sortOption = " ORDER BY t1.company, t2.fullname, t1.wage_type";
        $query = "SELECT t1.*, t2.fullname 
        FROM payroll_items as t1
        LEFT JOIN employees t2
        ON t1.company=t2.company AND t1.employee_id=t2.employee_id
        " . $queryOptions["condition"] . $sortOption;

        // echo "\n---> ".$queryOptions["condition"]."---\n";
        return $this->select($query, $queryOptions["params"], $sort);
    }

    public function getEmployeePayrolls($company, $reqParams, $limit = 0)
    {
        if ($company) {
            array_push($reqParams["condition"], array("company" => $company));
        }
        $mapFileType = array(
            "t1.company" => array("s", "upper"),
            // "username"=>Array("s", "upper"),
            "t1.wage_type" => array("s", "upper"),
            "t1.employee_id" => array("s", "upper"),
            "t1.payroll_date" => array("s", "regex"),
            "t1.status" => array("s", "upper"),
        );
        $sort = isset($reqParams["sort"]) ? $reqParams["sort"] : "";
        // print_r($reqParams["condition"]);

        $conditions = Array();
        foreach($reqParams["condition"] as $condItem) {
            $condEl = Array();
            foreach($condItem as $key=>$value) {
                $condEl["t1.$key"] = $value;
            }
            array_push($conditions, $condEl);
        }
        // print_r($conditions);

        $queryOptions = getQueryOptions($mapFileType, $conditions); //$reqParams["condition"]);

        // print_r($queryOptions);

        /*
        SELECT t1.*, t2.fullname, t2.id_number, t2.position, t2.department FROM payroll_items as t1
        LEFT JOIN employees t2
        ON t1.company=t2.company AND t1.employee_id=t2.employee_id
        WHERE (t1.payroll_date  LIKE '2023-12%') AND (UPPER(t1.status)=? ) ORDER BY t1.company, t1.employee_id, t1.wage_type
        */

        $sortOption = " ORDER BY t1.company, t1.employee_id, t1.wage_type";
        // $query = "SELECT t1.*, t2.fullname, t2.id_number, t3.total_wh_amount, t3.total_sso_amount, t4.rec_id as salary_ref_id, t4.salary, t4.effective_date as salary_effective_date, t4.department, t4.position 
        // FROM payroll_items as t1
        // LEFT JOIN employees t2
        // ON t1.company=t2.company AND t1.employee_id=t2.employee_id
        // LEFT JOIN deduct_collections t3        
        // ON t1.company=t4.company AND t1.employee_id=t4.employee_id AND
	    // t4.effective_date = (SELECT MAX(effective_date) FROM work_history as t5 WHERE t1.company=t5.company AND t1.employee_id=t5.employee_id AND status='APPROVED')


        // $query = "SELECT t1.*, t2.fullname, t2.id_number, t3.total_wh_amount, t3.total_sso_amount, t4.rec_id as salary_ref_id, t4.salary, t4.effective_date as salary_effective_date, t4.department, t4.position 
        // FROM payroll_items as t1
        // LEFT JOIN employees t2
        // ON t1.company=t2.company AND t1.employee_id=t2.employee_id
        // LEFT JOIN deduct_collections t3        
        // ON t1.company=t3.company AND t1.employee_id=t3.employee_id
        // LEFT JOIN work_history t4       
        // ON t1.company=t4.company AND t1.employee_id=t4.employee_id AND
        //     t4.effective_date = (SELECT MAX(effective_date) FROM work_history as t5 WHERE t1.company=t5.company AND t1.employee_id=t5.employee_id AND t1.status='APPROVED')" . 
 
        $query = "SELECT t1.*, t2.fullname, t2.id_number, t3.total_wh_amount, t3.total_sso_amount, t4.rec_id as salary_ref_id, t4.salary, t4.effective_date as salary_effective_date, t4.department, t4.position 
        FROM payroll_items as t1
        LEFT JOIN employees t2
        ON t1.company=t2.company AND t1.employee_id=t2.employee_id
        LEFT JOIN deduct_collections t3        
        ON t1.company=t3.company AND t1.employee_id=t3.employee_id
        LEFT JOIN work_history t4       
        ON t1.company=t4.company AND t1.employee_id=t4.employee_id AND
            t4.effective_date = (SELECT MAX(effective_date) FROM work_history as t5 WHERE t1.company=t5.company AND t1.employee_id=t5.employee_id AND status='APPROVED') "
        .$queryOptions["condition"]. "AND t1.status='APPROVED'" . $sortOption;

        //WHERE (UPPER(t1.employee_id)=? ) AND (t1.payroll_date  LIKE ?) AND (UPPER(t1.company)=? )  AND t1.status='APPROVED' ORDER BY t1.company, t1.employee_id, t1.wage_type";
 
 //WHERE (t1.payroll_date  LIKE '2024-01%') ORDER BY t1.company, t1.employee_id, t1.wage_type


        // -- LEFT JOIN (SELECT *, type as leave_type, SUM(days) as total_days FROM employee_leaves as t0 WHERE status='APPROVED' GROUP BY company, employee_id, type) t4
        // -- ON t1.company=t4.company AND t1.employee_id=t4.employee_id

        // echo $queryOptions["condition"] . $sortOption;
        // echo "\n\n".$query."\n";
        // print_r($queryOptions["params"]);
        return $this->select($query , $queryOptions["params"], $sort);
    }

    public function getPayrollInfo($company, $reqParams, $limit = 0)
    {
        if ($company) {
            array_push($reqParams["condition"], array("company" => $company));
        }
        $mapFileType = array(
            "company" => array("s", "upper"),
            // "username"=>Array("s", "upper"),
            "wage_type" => array("s", "upper"),
            "employee_id" => array("s", "upper"),
            "payroll_date" => array("s", "regex"),
            "status" => array("s", "upper"),
        );
        $sort = isset($reqParams["sort"]) ? $reqParams["sort"] : "";
        // print_r($reqParams);
        $queryOptions = getQueryOptions($mapFileType, $reqParams["condition"]);

        // print_r($sort);
        $sortOption = " ORDER BY company, employee_id, wage_type";
        $query = "SELECT * FROM payroll_items " . $queryOptions["condition"] . $sortOption;

/*
SELECT t1.company, t1.employee_id, t2.fullname, t3.salary, t1.wage_type, SUM(amount) as amount, t4.total_wh_amount, t4.total_sps_amount FROM payroll_items as t1
LEFT JOIN employees t2
ON t1.company=t2.company AND t1.employee_id=t2.employee_id 



LEFT JOIN salary t3
ON t1.company=t3.company AND t1.employee_id=t3.employee_id 

LEFT JOIN deduct_collections t4
ON t1.company=t4.company AND t1.employee_id=t4.employee_id 

WHERE t1.status='APPROVED' AND payroll_date LIKE '2023-12%'
GROUP BY t1.company, t1.employee_id, wage_type
ORDER BY t1.company, t1.employee_id, wage_type
*/
        // echo "\n---> ".$queryOptions["condition"]."---\n";
        return $this->select($query , $queryOptions["params"], $sort);
    }


    public function getPayrollSlip($company, $reqParams, $limit = 0)
    {
        if ($company) {
            array_push($reqParams["condition"], array("company" => $company));
        }
        $mapFileType = array(
            "company" => array("s", "upper"),
            // "username"=>Array("s", "upper"),
            // "email"=>Array("s", "lower"),
            "employee_id" => array("s", "upper"),
            "payroll_date" => array("s", "regex"),
            "status" => array("s", "upper"),
        );
        $sort = isset($reqParams["sort"]) ? $reqParams["sort"] : "";
        // print_r($reqParams);
        $queryOptions = getQueryOptions($mapFileType, $reqParams["condition"]);

        // print_r($queryOptions);
        // echo "\n---> ".$queryOptions["condition"]."---\n";
        /*
SELECT SUM(payroll_items.amount) as total, payroll_items.*, employees.fullname_en as fullname FROM `payroll_items`
LEFT JOIN employees ON payroll_items.employee_id=employees.employee_id 
WHERE   payroll_items.wage_type IN("SALARY", "ALLOWANCE", "JINCENTIVE", "OACHIEVED", "OT", "PCHARGE", "ONSITE", "BONUS") AND 
        payroll_items.status IN('APPROVED', 'PAID') AND 
        payroll_items.payroll_date LIKE 'YYYY-MM%'
GROUP BY employee_id, wage_type


SELECT  company, employee_id, payroll_date, SUM(amount) as total FROM `payroll_items`
WHERE   wage_type IN("SALARY", "ALLOWANCE", "JINCENTIVE", "OACHIEVED", "OT", "PCHARGE", "ONSITE", "BONUS") AND 
        status IN('APPROVED', 'PAID') AND 
        payroll_date LIKE 'YYYY-MM%'
GROUP BY employee_id

*/
        return $this->select("SELECT * FROM payroll_items " . $queryOptions["condition"], $queryOptions["params"], $sort);
    }

    function getLeaveSummary($company, $reqParams, $sort)
    {
        if ($company) {
            array_push($reqParams["condition"], array("company" => $company));
        }

        $mapFieldType = array(
            "company" => array("s", "upper"),
            "employee_id" => array("s", "upper"),
            "start_date" => array("s", ""),

            "employee_leaves.status" => array("s", ""),
        );

        $condition = array();
        foreach ($reqParams["condition"] as $group) {
            $newGroup = array();
            foreach ($group as $key => $value) {
                $newGroup[$key] = $value;
            }
            array_push($condition, $newGroup);
        }

        // print_r($condition);

        $reqParams["condition"] = $condition;
        $queryOptions = getQueryOptions($mapFieldType, $reqParams["condition"]);

        // echo "Start Model\n";
        // print_r($queryOptions);
        // echo "\nEnd Model\n";

        $sort = isset($reqParams["sort"]) ? (" ORDER BY " . $reqParams["sort"]["key"] . " " . $reqParams["sort"]["order"]) : "";


        //WHERE (UPPER(employee_id)=? ) AND (start_date  LIKE '2023-01%') AND (UPPER(company)=? )
        $condition =
            $query = "SELECT SUBSTRING(start_date, 1, 7) as month_year, type, status, COUNT(*) as count FROM `employee_leaves` {$queryOptions['condition']} AND status = \"APPROVED\" GROUP BY type, SUBSTRING(start_date, 1, 7), status";

        // echo $query;

        return $this->select($query, $queryOptions["params"], []); //$sort);

    }

    // public function updateLeave($reqParams){
    //     $query = "UPDATE employee_leaves SET ".
    //     "username=?, email=?, fullname=?, gender=?, birthdate=?, ".
    //     "enroll_date=?, contact_no=?, contact_person=?, role=?, department=?".
    //     " WHERE employee_id=?";
    //     return $this->update($query, 
    //         Array("sssisssssss", 
    //         $reqParams["username"], $reqParams["email"], $reqParams["fullname"],
    //         $reqParams["gender"], $reqParams["birthdate"], $reqParams["enroll_date"], $reqParams["contact_no"],
    //         $reqParams["contact_person"], $reqParams["role"], $reqParams["department"], $reqParams["employee_id"]
    //     ));
    // }

    // "recId"=>"rec_id",
    // "employeeId"=>"employee _id",
    // "itemDate"=>"payroll_date",
    // "type"=>"wage_type",
    // "amount"=>"amount",
    // "taxRate"=>"tax_rate",
    // "payMethod"=>"pay_method",
    // "status"=>"status",
    // "approvedBy"=>"approved_by",
    // "approveDate"=>"approve_date",
    // "approveNote"=>"approval_note",

    public function updatePayroll($condParams, $updateData)
    {
        $mapFieldType = array(
            "rec_id" => array("i", "upper"),
            "company" => array("s", "upper"),
            "employee_id" => array("s", "upper"),
            "payroll_date" => array("s", ""),
            "wage_type" => array("s", ""),
            "amount" => array("d", ""),
            "tax_rate" => array("d", ""),
            "pay_method" => array("s", ""),
            "status" => array("s", ""),
            "approved_by" => array("s", ""),
            "approve_date" => array("s", ""),
            "approval_note" => array("s", ""),
        );


        $setParams = array();
        $setFields = "";
        $format = "";

        if (isset($updateData["rec_id"]) && strlen($updateData["rec_id"])) {
            $setFields .= "rec_id=?,";
            $format .= "s";
            array_push($setParams, $updateData["rec_id"]);
        }
        if (isset($updateData["employee_id"]) && strlen($updateData["employee_id"])) {
            $setFields .= "employee_id=?,";
            $format .= "s";
            array_push($setParams, $updateData["employee_id"]);
        }
        if (isset($updateData["payroll_date"]) && strlen($updateData["payroll_date"])) {
            $setFields .= "payroll_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["payroll_date"]);
        }
        if (isset($updateData["wage_type"]) && strlen($updateData["wage_type"])) {
            $setFields .= "wage_type=?,";
            $format .= "s";
            array_push($setParams, $updateData["wage_type"]);
        }
        if (isset($updateData["amount"]) && strlen($updateData["amount"])) {
            $setFields .= "amount=?,";
            $format .= "d";
            array_push($setParams, $updateData["amount"]);
        }
        if (isset($updateData["tax_rate"]) && strlen($updateData["tax_rate"])) {
            $setFields .= "tax_rate=?,";
            $format .= "d";
            array_push($setParams, $updateData["tax_rate"]);
        }
        if (isset($updateData["pay_method"]) && strlen($updateData["pay_method"])) {
            $setFields .= "pay_method=?,";
            $format .= "s";
            array_push($setParams, $updateData["pay_method"]);
        }
        if (isset($updateData["status"]) && strlen($updateData["status"])) {
            $setFields .= "status=?,";
            $format .= "s";
            array_push($setParams, $updateData["status"]);
        }
        if (isset($updateData["approved_by"]) && strlen($updateData["approved_by"])) {
            $setFields .= "approved_by=?,";
            $format .= "s";
            array_push($setParams, $updateData["approved_by"]);
        }
        if (isset($updateData["approve_date"]) && strlen($updateData["approve_date"])) {
            $setFields .= "approve_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["approve_date"]);
        }
        if (isset($updateData["approval_note"]) && strlen($updateData["approval_note"])) {
            $setFields .= "approval_note=?,";
            $format .= "s";
            array_push($setParams, $updateData["approval_note"]);
        }

        $setFields = rtrim($setFields, ",");
        $queryOptions = getQueryOptions($mapFieldType, $condParams);

        // echo "\n----- setFields -----\n";
        // print_r($queryOptions);

        // echo "\n----- condParams -----\n";
        // print_r($setParams);


        $query = "UPDATE payroll_items SET " . $setFields . $queryOptions["condition"];
        $queryOptions["params"][0] = $format . $queryOptions["params"][0];
        $updateParams = array($queryOptions["params"][0]);
        $updateParams = array_merge($updateParams, $setParams);
        array_shift($queryOptions["params"]);
        $updateParams = array_merge($updateParams, $queryOptions["params"]);
        // echo "\n----- $query -----\n";
        // print_r($updateParams);

        return $this->update($query, $updateParams);
    }


    function importSalaryToPayroll($monthYear)
    {
        // echo "\n\nmonthYear:". $monthYear."\n\n";
        $dt = explode("-", $monthYear);
        $lastDate = cal_days_in_month(CAL_GREGORIAN, $dt[1], $dt[0]);
        $salaryDate = sprintf("%s-%02d 00:00:00", $monthYear, $lastDate);
        // echo "\n\nsalaryDate:". $salaryDate."\n\n";

        $query = "INSERT INTO payroll_items (company, employee_id, wage_type, amount, payroll_date, tax_rate, status, item_ref_no)
        SELECT t1.company, t1.employee_id, 'SALARY', t1.salary, '$salaryDate', 3, 'WAITING', t1.rec_id FROM 
            work_history as t1,
            (SELECT *, MAX(effective_date) as effective_date2 FROM work_history
            WHERE status = 'APPROVED' GROUP BY employee_id, rec_id ) as t2
        WHERE t1.effective_date = t2.effective_date2 AND t1.company=t2.company AND t1.employee_id=t2.employee_id";
        // echo "\n\n Import salary: ".$query."\n\n";
        return $this->update($query);
    }

    function importPhoneAllowanceToPayroll($monthYear, $phoneAllowanceList, $pChargeValue) {
        $dt = explode("-", $monthYear);
        $lastDate = cal_days_in_month(CAL_GREGORIAN, $dt[1], $dt[0]);
        $salaryDate = sprintf("%s-%02d 00:00:00", $monthYear, $lastDate);

        $values = "VALUES ";
        foreach($phoneAllowanceList as $salary) {
            $values .= ("('".$salary["company"]."', '".$salary["employee_id"]."', 'Auto inserted by Payroll', 'PCHARGE', $pChargeValue, '$salaryDate', 0, 'WAITING', 0),");
        }
        $values = rtrim($values, ',');
        $query = "INSERT INTO payroll_items (company, employee_id, item_note, wage_type, amount, payroll_date, tax_rate, status, item_ref_no)".
        $values;

        // echo "\nphoneAllowanceList\n";
        // print_r($phoneAllowanceList);
        // echo "\nphoneAllowanceList\n";
        return $this->update($query);
    }

    function getParollItems($type, $monthYear) {
        $query = "SELECT * FROM payroll_items
        WHERE wage_type='$type' AND payroll_date LIKE '$monthYear%'";

        // echo "\n\nQuery Salary".$query."\n\n";
        return $this->select($query);
    }

    
    function importExpenseToPayroll($monthYear) {
        $cfgCutDate = 25;
        $dtTokens = explode("-", $monthYear);
        $startDate = "";
        $endDate = $monthYear.sprintf("-%02d", $cfgCutDate);
        if( $dtTokens[1] == "01"){
            // Last year
            $startDate = ($dtTokens[0] - 1) ."-12-".sprintf("%02d", $cfgCutDate+1);
        } else {
            $startDate = $dtTokens[0]."-".sprintf("%02d", ($dtTokens[1]*1-1))."-".sprintf("%02d", $cfgCutDate+1);
        }

        // SELECT * FROM `expenses` where employee_id='OWLS0002' AND item_date BETWEEN "2023-12-26" AND "2024-01-25" 
        // SELECT * FROM `payroll_items` where employee_id='OWLS0002' AND payroll_date BETWEEN "2023-12-26" AND "2024-01-25" 

        $query = "INSERT INTO payroll_items (company, employee_id, wage_type, amount, payroll_date, tax_rate, status, item_ref_no)
        SELECT MAX(company), MAX(employee_id), 'EXPENSE', SUM(amount) as amount, MAX(item_date), 0, 'WAITING', MAX(rec_id) 
        FROM expenses 
        WHERE status = 'APPROVED' AND item_date LIKE ? GROUP BY company, rec_id, employee_id";
        // WHERE status = 'APPROVED' AND (item_date BETWEEN ? AND ?) GROUP BY company, employee_id";

        // echo "\n\nimportExpenseToPayroll\n\n".$query."\n";
        // echo $startDate." - ".$endDate."\n";
        // return $this->update($query, array("ss", $startDate, $endDate));
        return $this->update($query, array("s", "$monthYear%"));
    }


    function importJobRouteToPayroll($monthYear) {
        $query = "INSERT INTO payroll_items (company, employee_id, wage_type, amount, payroll_date, tax_rate, status, item_ref_no)
        SELECT MAX(company), MAX(employee_id), 'TRANSPORT', SUM(distance*6) as amount, MAX(routing_date), 0, 'WAITING', MAX(rec_id)
        FROM job_routes 
        WHERE routing_date LIKE ? AND status='APPROVED'
        GROUP By company, employee_id";

        return $this->update($query, array("s", "$monthYear%"));
    }


    function importCommissionToPayroll($monthYear)
    {
        $query = "INSERT INTO payroll_items (company, employee_id, wage_type, amount, payroll_date, tax_rate, status, item_ref_no)
    SELECT MAX(company), MAX(employee_id), 'COMMISSION', SUM(amount) as amount, MAX(issue_date), 0, 'WAITING', MAX(rec_id)
    FROM commissions 
    WHERE type='COMMISSION' AND issue_date LIKE ? AND status='APPROVED'
    GROUP By company, employee_id";

        return $this->update($query, array("s", "$monthYear%"));
    }

    function importIncentiveToPayroll($monthYear)
    {
        $query = "INSERT INTO payroll_items (company, employee_id, wage_type, amount, payroll_date, tax_rate, status, item_ref_no)
    SELECT MAX(company), MAX(employee_id), 'JINCENTIVE', SUM(amount) as amount, MAX(issue_date), 0, 'WAITING', MAX(rec_id)
    FROM commissions 
    WHERE type LIKE 'AREA%' AND issue_date LIKE ? AND status='APPROVED'
    GROUP By company, employee_id";

        return $this->update($query, array("s", "$monthYear%"));
    }


    /*
SELECT t0.company, t0.employee_id, 'JINCENTIVE', SUM(TIMESTAMPDIFF(HOUR, t0.start_datetime, t0.end_datetime)) as hour,TIMESTAMPDIFF(HOUR, t0.start_datetime, t0.end_datetime)*1.5*(t3.salary/30/8) as amount, t3.salary, start_datetime, 0, 'WAITING', t0.rec_id
FROM overtimes as t0
LEFT JOIN (

    SELECT t1.company, t1.employee_id, t1.salary FROM 
            salary as t1,
            (SELECT *, MAX(effective_date) as effective_date2 FROM salary
            WHERE status = 'APPROVED' GROUP BY employee_id ) as t2
        WHERE t1.effective_date = t2.effective_date2 AND t1.company=t2.company AND t1.employee_id=t2.employee_id
    ) as t3
    ON t0.company=t3.company AND t0.employee_id=t3.employee_id
    
    GROUP By company, employee_id
    */
    function getActiveSalaryList()
    {
        $query = "SELECT t1.company, t1.employee_id, t1.salary FROM 
        work_history as t1,
        (SELECT *, MAX(effective_date) as effective_date2 FROM work_history
        WHERE status = 'APPROVED' GROUP BY employee_id, rec_id ) as t2
    WHERE t1.effective_date = t2.effective_date2 AND t1.company=t2.company AND t1.employee_id=t2.employee_id";
        // echo $query;
        return $this->select($query);
    }

    function getEmployeeOvertime($monthYear)
    {
        $query = "SELECT MAX(company) as company, MAX(employee_id) as employee_id, SUM(TIMESTAMPDIFF(HOUR, start_datetime, end_datetime)) as hours, MAX(start_datetime) as start_datetime, MAX(rec_id) as rec_id
        FROM overtimes
        WHERE start_datetime LIKE ? AND status='APPROVED'
        GROUP BY company, employee_id";
        return $this->select($query, array("s", "$monthYear%"));
    }

    function importOvertimeToPayroll($insertData)
    {
        $query = "INSERT INTO payroll_items (company, employee_id, wage_type, amount, payroll_date, tax_rate, status, item_ref_no, item_note) VALUES ". $insertData;
    //     $query = "SELECT t0.company, t0.employee_id, 'OVERTIME', TIMESTAMPDIFF(HOUR, t0.start_datetime, t0.end_datetime)*1.5*(t3.salary/30/8) as amount, start_datetime, 0, 'WAITING', t0.rec_id, SUM(TIMESTAMPDIFF(HOUR, t0.start_datetime, t0.end_datetime)) as hour
    // FROM overtimes as t0
    // LEFT OUTER JOIN (
    
    //     SELECT t1.company, t1.employee_id, t1.salary FROM 
    //             salary as t1,
    //             (SELECT *, MAX(effective_date) as effective_date2 FROM salary
    //             WHERE status = 'APPROVED' GROUP BY employee_id ) as t2
    //         WHERE t1.effective_date = t2.effective_date2 AND t1.company=t2.company AND t1.employee_id=t2.employee_id
    //     ) as t3
    //     ON t0.company=t3.company AND t0.employee_id=t3.employee_id AND t0.start_datetime LIKE '2023-12%'
        
    //     GROUP By company, employee_id";
        // echo $query;
        return $this->update($query);
    }

    /*
INSERT INTO deduct_collections
  (rec_id, company, employee_id, total_wh_amount, total_sso_amount, last_wh_amount, last_sso_amount, status, approve_date, approved_by, approval_note, last_update)
VALUES
  ('OWL.OWL9991', 'OWL', 'OWL9991', 0, 0, 100, 50, 'APPROVED', NOW(), 'UADMIN', 'Create payroll', NOW())
ON DUPLICATE KEY UPDATE
  total_wh_amount     = total_wh_amount + 100,
  total_sso_amount = total_sso_amount + 50,
  last_wh_amount= 100,
  last_sso_amount= 50,
  status= 'APPROVED',
  approve_date= NOW(),
  approved_by= 'UADMIN',
  approval_note= 'Create payroll',
  last_update= NOW()

  "OWLOWL9990": {
            "company": "OWL",
            "employeeId": "OWL9990",
            "sso": 750,
            "tax": 1455.6250499999999,
            "totalIncome": 97041.67
        },
    */
    function updateDeductCollection($data)
    {
        // $query = "INSERT INTO payroll_items (company, employee_id, wage_type, amount, payroll_date, tax_rate, status, item_ref_no, item_note) VALUES ". $strOvertime;
        foreach($data as $key=>$item) {
        $query = "INSERT INTO deduct_collections
        (rec_id, company, employee_id, total_wh_amount, total_sso_amount, last_wh_amount, last_sso_amount, status, is_rollback, approve_date, approved_by, approval_note, last_update)
      VALUES
        ('".$item["company"].".".$item["employeeId"]."', '".$item["company"]."', '".$item["employeeId"]."', 0, 0, ".$item["tax"].", ".$item["sso"].", 'APPROVED', false, NOW(), 'UADMIN', 'Create payroll', NOW())
      ON DUPLICATE KEY UPDATE
        last_wh_amount= total_wh_amount,
        last_sso_amount= total_sso_amount,
        total_wh_amount= total_wh_amount + ".$item["tax"].",
        total_sso_amount= total_sso_amount + ".$item["sso"].",
        status= 'APPROVED',
        is_rollback=false,
        approve_date= NOW(),
        approved_by= 'UADMIN',
        approval_note= 'Create payroll',
        last_update= NOW()";
        // echo "$query\n";
        $this->update($query);
        }
        return true;//$this->update($query);
    }
    function updatePayrollItemRefNo($refNo, $company, $monthYear) {
        $query = "UPDATE payroll_items SET ref_no='$refNo' WHERE payroll_date LIKE '$monthYear%' AND company='$company'";
        return $this->update($query);
    }
    
    function createPayrollTransaction($payrolls)
    {
        // print_r($payrolls);
        $query = 'INSERT INTO payrolls (ref_no, company, issue_date, num_employee, amount, status) VALUES ';

        $values = '';
        try{
        foreach($payrolls as $company=>$employees) {
            $numEmployee = 0;
            $amount = 0;
            foreach($employees as $employeeId => $item) {
                // print_r($item[0]);
                $refNo = $company.date("YmdHis");
                $numEmployee++;
                $amount += $item[0];
            }
            $values .= '("'.$refNo.'", "'.$company.'", "'.date("Y-m-d H:i:s").'", '.$numEmployee.', '.$amount.', "APPROVED"),';
            $this->updatePayrollItemRefNo($refNo, $company, date("Y-m"));
        }
    }catch(Error $err) {
        print_r($err);
    }
        // echo $query.$values;

        $values = rtrim($values, ',');
        $query .= $values;

        echo $query;
        return $this->update($query);
    }

    /*
UPDATE deduct_collections
SET total_wh_amount=last_wh_amount, total_sso_amount=last_sso_amount, last_wh_amount=0, last_sso_amount=0, is_rollback=true
WHERE approve_date LIKE '2023-12%' AND status='APPROVED' AND is_rollback=false
*/
    function rollbackDeductCollection($monthYear)
    {
        $query =      "UPDATE deduct_collections
        SET total_wh_amount=last_wh_amount, total_sso_amount=last_sso_amount, last_wh_amount=0, last_sso_amount=0, is_rollback=true
        WHERE approve_date LIKE ? AND status='APPROVED' AND is_rollback=false";

        return $this->update($query, array("s", "$monthYear%"));
    }


    function undoImportToPayroll($monthYear)
    {
        $query =      "DELETE FROM payroll_items WHERE payroll_date LIKE ? AND item_ref_no IS NOT NULL";
        // $query = "SELECT * FROM payroll_items WHERE payroll_date LIKE ? AND item_ref_no IS NOT NULL";

        return $this->update($query, array("s", "$monthYear%"));
    }

    function addPayrollAdditional($company, $item)
    {
        $query = "SELECT * FROM payroll_items WHERE company=? AND employee_id=? AND wage_type=? AND payroll_date LIKE ?";
        $result = $this->select($query, Array("ssss", $company, $item["employee_id"], $item["type"], substr($item["payroll_date"], 0, 7)."%" ), []); //$sort);
        // print_r($result);
        // echo "\n=======\n";
        $params = Array();
        if(sizeof($result)) {
            $query = "UPDATE payroll_items SET 
            payroll_date= ?,
            wage_type= ?,
            amount= ?,
            status= ?
            WHERE company=? AND employee_id=? AND wage_type=? AND payroll_date LIKE ?";
            $params = Array("ssdsssss", $item["payroll_date"], $item["type"], $item["amount"], $item["status"], $company, $item["employee_id"], $item["type"],substr($item["payroll_date"], 0, 7)."%");
        } else {
            $query = "INSERT INTO payroll_items
            (company, employee_id, payroll_date, wage_type, amount, status)
            VALUES
            (?, ?, ?, ?, ?, ?)";
            $params = Array("ssssds", $company, $item["employee_id"], $item["payroll_date"], $item["type"], $item["amount"], $item["status"]);
        }
        // echo "$query\n";
        // print_r($params);
        return $this->update($query, $params);
    }
}
