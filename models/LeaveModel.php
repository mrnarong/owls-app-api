<?php
require_once PROJECT_ROOT_PATH . "/models/Database.php";
class LeaveModel extends Database{

    public function addLeave($company, $reqParams){
        // echo "\n=======\n";
        // print_r($reqParams["documents"]);
        // echo "\n========\n";

        $query = "INSERT INTO employee_leaves ".
        "(company, employee_id, type, leave_reason, start_date, end_date, days".(isset($reqParams["documents"])?", documents":"").")".
        "VALUES (?, ?, ?, ?, ?, ?, ?".(isset($reqParams["documents"])?", ?":"").")";
        
        $dataParams = Array(
            "ssssssd".(isset($reqParams["documents"])?"s":""),
            $company, $reqParams["employee_id"], 
            $reqParams["type"], 
            $reqParams["leave_reason"],
            $reqParams["start_date"], 
            $reqParams["end_date"], 
            $reqParams["days"]
        );
        if(isset($reqParams["documents"])) {
            array_push($dataParams, $reqParams["documents"]);
        }
        if($this->insert($query, $dataParams)) {
            return $this->getInsertId();
        }
        return -1;
    }

    //  SELECT employee_leaves.*, employees.fullname FROM employee_leaves 
    //  LEFT JOIN employees ON employee_leaves.employee_id=employees.employee_id 
    //  WHERE employee_leaves.employee_id='OWL0001' employee_leaves.employee_id=?
    //  ORDER BY employee_leaves.issue_date

    public function getLeaves($company, $reqParams, $limit=0){
        if($company) {
            array_push($reqParams["condition"], Array("company" => $company));
        }
        $mapFieldType = Array(
            "t1.company"=>Array("s", "upper"),
            // "employee_leaves.employee_id"=>Array("s", "upper"),
            // "employee_leaves.type"=>Array("s", "upper"),
            // "employee_leaves.status"=>Array("s", "upper"),
            "t1.employee_id"=>Array("s", "upper"),
            "t1.type"=>Array("s", "upper"),
            // "employee_leaves.status"=>Array("s", "upper"),
            "t1.issue_date"=>Array("s", ""),
            "t1.status"=>Array("s", "upper"),
        );

        // echo "Start Model\n";
        // print_r($reqParams);
        // echo "\nEnd Model\n";

        // Remapping WHERE clause fields for JOIN
        $condition = Array();
        foreach($reqParams["condition"] as $group){
            $newGroup = Array();
            foreach($group as $key=>$value){
                // echo "\n".$value."\n";
               $newGroup["t1.".$key] = $value;
            }
            array_push($condition, $newGroup);
        }

        // print_r($condition);

        $reqParams["condition"] = $condition;
        $queryOptions = getQueryOptions($mapFieldType, $reqParams["condition"]);

        // echo "Start Model\n";
        // print_r($queryOptions);
        // echo "\nEnd Model\n";

        $sort = isset($reqParams["sort"]) ? (" ORDER BY " . $reqParams["sort"]["key"]." " . $reqParams["sort"]["order"]) : "";
        
        $sortOption = " ORDER BY t1.company, t2.fullname, t1.type";
        $query = "SELECT t1.*, t1.approval_note as approve_note, t2.fullname as fullname 
        FROM employee_leaves as t1 
        LEFT JOIN employees t2
        ON t1.company=t2.company AND t1.employee_id=t2.employee_id".
        $queryOptions["condition"] . $sortOption;

        // echo $query."\n";

        return $this->select($query, $queryOptions["params"], []); //$sort);
    }

    function getLeaveSummary($company, $reqParams, $sort) {
        if($company) {
            array_push($reqParams["condition"], Array("company" => $company));
        }

        // print_r($reqParams);

        $mapFieldType = Array(
            "company"=>Array("s", "upper"),
            "employee_id"=>Array("s", "upper"),
            "start_date"=>Array("s", ""),

            "status"=>Array("s", ""),
        );

        $condition = Array();
        foreach($reqParams["condition"] as $group){
            $newGroup = Array();
            foreach($group as $key=>$value){
               $newGroup[$key] = $value;
            }
            array_push($condition, $newGroup);
        }

        // print_r($condition);

        $reqParams["condition"] = $condition;
        $queryOptions = getQueryOptions($mapFieldType, $reqParams["condition"]);

        // echo "Start Model\n";
        // print_r($queryOptions["condition"]);
        // echo "\nEnd Model\n";

        $sort = isset($reqParams["sort"]) ? (" ORDER BY " . $reqParams["sort"]["key"]." " . $reqParams["sort"]["order"]) : "";


        //WHERE (UPPER(employee_id)=? ) AND (start_date  LIKE '2023-01%') AND (UPPER(company)=? )
        $condition = str_replace("payroll_date", "start_date", $queryOptions['condition']);
        $query = "SELECT company, employee_id, SUBSTRING(start_date, 1, 7) as month_year, type, status, SUM(days) as total_days 
        FROM `employee_leaves` 
        {$condition}
        GROUP BY type, company, employee_id, SUBSTRING(start_date, 1, 7), status";

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

    public function updateLeave($company, $reqCondition, $updateData){
        if($company) {
            array_push($reqCondition, Array("company" => $company));
        }
        $mapFieldType = Array(
            "company"=>Array("s", "upper"),
            "employee_id"=>Array("s", "upper"),
            "username"=>Array("s", "upper"),
            "rec_id"=>Array("i", ""),
        );

        $condition = Array();
        foreach($reqCondition as $group){
            $newGroup = Array();
            foreach($group as $key=>$value){
                $newGroup[$key] = $value;
            }
            array_push($condition, $newGroup);
        }

        $setParams = Array();
        $setFields = "";
        $format = "";

        if(isset($updateData["employeeId"]) && strlen($updateData["employeeId"])) {
            $setFields .= "employee_id=?,";
            $format .= "s";
            array_push($setParams, $updateData["employeeId"]);
        }
        if(isset($updateData["type"]) && strlen($updateData["type"])) {
            $setFields .= "type=?,";
            $format .= "s";
            array_push($setParams, $updateData["type"]);
        }
        if(isset($updateData["leaveReason"]) && strlen($updateData["leaveReason"])) {
            $setFields .= "leave_reason=?,";
            $format .= "s";
            array_push($setParams, $updateData["leaveReason"]);
        }
        if(isset($updateData["issueDate"]) && strlen($updateData["issueDate"])) {
            $setFields .= "issue_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["issueDate"]);
        }
        if(isset($updateData["startDate"]) && strlen($updateData["startDate"])) {
            $setFields .= "start_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["startDate"]);
        }
        if(isset($updateData["endDate"]) && strlen($updateData["endDate"])) {
            $setFields .= "end_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["endDate"]);
        }
        if(isset($updateData["approvedBy"]) && strlen($updateData["approvedBy"])) {
            $setFields .= "approved_by=?,";
            $format .= "s";
            array_push($setParams, $updateData["approvedBy"]);
        }
        if(isset($updateData["approveDate"]) && strlen($updateData["approveDate"])) {
            $setFields .= "approve_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["approveDate"]);
        }
        if(isset($updateData["approveStatus"]) && strlen($updateData["approveStatus"])) {
            $setFields .= "status=?,";
            $format .= "s";
            array_push($setParams, $updateData["approveStatus"]);
        }
        if(isset($updateData["approveNote"]) && strlen($updateData["approveNote"])) {
            $setFields .= "approval_note=?,";
            $format .= "s";
            array_push($setParams, $updateData["approveNote"]);
        }
        if(isset($updateData["remark"]) && strlen($updateData["remark"])) {
            $setFields .= "remark=?,";
            $format .= "s";
            array_push($setParams, $updateData["remark"]);
        }
        $setFields = rtrim($setFields, ",");
        $queryOptions = getQueryOptions($mapFieldType, $condition);

        $query = "UPDATE employee_leaves SET ".$setFields.$queryOptions["condition"];
        $queryOptions["params"][0] = $format . $queryOptions["params"][0];
        $updateParams = Array($queryOptions["params"][0]);
        $updateParams = array_merge($updateParams, $setParams);
        array_shift($queryOptions["params"]);
        $updateParams = array_merge($updateParams, $queryOptions["params"]);

        return $this->update($query, $updateParams);
    }
}
?>