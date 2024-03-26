<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class LeaveModel extends Database{

    public function addLeave($reqParams){
        // print_r($reqParams);
// rec_id 	employee_id 	type 	leave_reason 	issue_date 	start_date 	end_date 	approve_date 	approve_status 	approve_reason 	remark 
        $query = "INSERT INTO employee_leaves ".
        "(employee_id, type, leave_reason, issue_date, start_date, end_date, approve_date, approve_status, approve_reason, remark)".
        "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->insert($query, 
            Array("ssssssssss", 
            $reqParams["employee_id"], $reqParams["type"], $reqParams["leave_reason"], $reqParams["issue_date"],
            $reqParams["start_date"], $reqParams["end_date"], $reqParams["approve_date"], "WAITING",
            $reqParams["approve_reason"], $reqParams["remark"]
        ));
    }

    //  SELECT employee_leaves.*, employees.fullname FROM employee_leaves 
    //  LEFT JOIN employees ON employee_leaves.employee_id=employees.employee_id 
    //  WHERE employee_leaves.employee_id='OWL0001' employee_leaves.employee_id=?
    //  ORDER BY employee_leaves.issue_date

    public function getLeaves($reqParams, $limit=0){
        $mapFileType = Array(
            "employee_leaves.employee_id"=>Array("s", "upper"),
            "employee_leaves.type"=>Array("s", "upper"),
            "employee_leaves.approve_status"=>Array("s", "upper"),
            "issue_date"=>Array("s", ""),
        );
        // print_r($reqParams["condition"]);

        // Remapping WHERE clause fields for JOIN
        $condition = Array();
        foreach($reqParams["condition"] as $group){
            $newGroup = Array();
            foreach($group as $key=>$value){
                // echo "\n".$value."\n";
               $newGroup["employee_leaves.".$key] = $value;
            }
            array_push($condition, $newGroup);
        }

        // print_r($condition);

        $reqParams["condition"] = $condition;
        $queryOptions = getQueryOptions($mapFileType, $reqParams["condition"]);
        // print_r($queryOptions["condition"]);
        // print_r($queryOptions["params"]);
        // return $this->select("SELECT * FROM employee_leaves ".$queryOptions["condition"], $queryOptions["params"], $reqParams["sort"]);
        return $this->select(
            "SELECT employee_leaves.*, employees.fullname as fullname FROM employee_leaves ".
            "LEFT JOIN employees ON employee_leaves.employee_id=employees.employee_id".
            $queryOptions["condition"], $queryOptions["params"], $reqParams["sort"]);
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

    public function updateLeave($condParams, $updateData){
        $mapFileType = Array(
            // "employee_id"=>Array("s", "upper"),
            // "username"=>Array("s", "upper"),
            // "user_email"=>Array("s", "lower"),
            // "company"=>Array("s", "upper"),
            "rec_id"=>Array("i", ""),
        );

        // print_r($updateData);
        $setParams = Array();
        $setFields = "";
        $format = "";

        if(isset($updateData["type"]) && strlen($updateData["type"])) {
            $setFields .= "type=?,";
            $format .= "s";
            array_push($setParams, $updateData["type"]);
        }
        if(isset($updateData["leave_reason"]) && strlen($updateData["leave_reason"])) {
            $setFields .= "leave_reason=?,";
            $format .= "s";
            array_push($setParams, $updateData["leave_reason"]);
        }
        if(isset($updateData["issue_date"]) && strlen($updateData["issue_date"])) {
            $setFields .= "issue_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["issue_date"]);
        }
        if(isset($updateData["start_date"]) && strlen($updateData["start_date"])) {
            $setFields .= "start_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["start_date"]);
        }
        if(isset($updateData["end_date"]) && strlen($updateData["end_date"])) {
            $setFields .= "end_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["end_date"]);
        }
        if(isset($updateData["approve_date"]) && strlen($updateData["approve_date"])) {
            $setFields .= "approve_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["approve_date"]);
        }
        if(isset($updateData["approve_status"]) && strlen($updateData["approve_status"])) {
            $setFields .= "approve_status=?,";
            $format .= "s";
            array_push($setParams, $updateData["approve_status"]);
        }
        if(isset($updateData["approve_reason"]) && strlen($updateData["approve_reason"])) {
            $setFields .= "approve_reason=?,";
            $format .= "s";
            array_push($setParams, $updateData["approve_reason"]);
        }
        if(isset($updateData["remark"]) && strlen($updateData["remark"])) {
            $setFields .= "remark=?,";
            $format .= "s";
            array_push($setParams, $updateData["remark"]);
        }
        $setFields = rtrim($setFields, ",");
        $queryOptions = getQueryOptions($mapFileType, $condParams);

        $query = "UPDATE employee_leaves SET ".$setFields.$queryOptions["condition"];

        $format .= $queryOptions["params"][0];
        array_unshift($setParams, $format);
        array_shift($queryOptions["params"]);
        $setParams = array_merge($setParams, $queryOptions["params"]); 

        return $this->update($query, $setParams);
    }
}
?>