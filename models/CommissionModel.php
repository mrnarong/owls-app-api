<?php
require_once PROJECT_ROOT_PATH . "/models/Database.php";
class CommissionModel extends Database{

    public function addCommission($company, $reqParams){
        // print_r($reqParams);
        $query = "INSERT INTO commissions ".
        "(company, employee_id, issue_date, role, amount, type, project, project_value)". // approve_date, approved_by, approval_note, 
        "VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->insert($query, Array(
            "ssssdssd",
            $company, $reqParams["employee_id"], $reqParams["issue_date"], $reqParams["role"], 
            $reqParams["amount"], $reqParams["type"], $reqParams["project"], $reqParams["project_value"]
        ));
    }

    public function getCommissions($company, $reqParams, $limit=0){
        if($company) {
            array_push($reqParams["condition"], Array("company" => $company));
        }
        $mapFieldType = Array(
            "t1.company"=>Array("s", "upper"),
            "t1.employee_id"=>Array("s", "upper"),
            "t1.issue_date"=>Array("s", "regex"),
            "t1.status"=>Array("s", "upper"),
            );

            $condition = Array();
            foreach($reqParams["condition"] as $group){
                $newGroup = Array();
                foreach($group as $key=>$value){
                   $newGroup["t1.".$key] = $value;
                }
                array_push($condition, $newGroup);
            }
    

        $sort = isset($reqParams["sort"]) ? $reqParams["sort"] : "";
        $sortOption = " ORDER BY t1.company, t2.fullname, t1.issue_date";
        $queryOptions = getQueryOptions($mapFieldType, $condition);
        

        $query = "SELECT t1.*, t2.fullname as fullname 
        FROM commissions as t1 ".
        "LEFT JOIN employees t2 
        ON t1.company=t2.company AND t1.employee_id=t2.employee_id".
        $queryOptions["condition"].$sortOption;

        // print_r($queryOptions);

        return $this->select($query, $queryOptions["params"], []);

        // print_r($queryOptions);
        // echo "\n---> ".$queryOptions["condition"]."---\n";
        // return $this->select("SELECT * FROM commissions ".$queryOptions["condition"], $queryOptions["params"], $sort);
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

    public function updateCommission($condParams, $updateData){
        $mapFieldType = Array(
            "rec_id"=>Array("i", "upper"),
            "company"=>Array("s", "upper"),
            "employee_id"=>Array("s", "upper"),
            "issue_date"=>Array("s", ""),
            "role"=>Array("s", ""),
            "amount"=>Array("d", ""),
            "type"=>Array("s", ""),
            "project"=>Array("s", ""),
            "project_value"=>Array("d", ""),
            "percent"=>Array("d", ""),
            "status"=>Array("s", ""),
            "approved_by"=>Array("s", ""),
            "approve_date"=>Array("s", ""),
            "approval_note"=>Array("s", ""),
        );
      

        $setParams = Array();
        $setFields = "";
        $format = "";
        
        if(isset($updateData["rec_id"]) && strlen($updateData["rec_id"])) {
            $setFields .= "rec_id=?,";
            $format .= "s";
            array_push($setParams, $updateData["rec_id"]);
        }
        if(isset($updateData["employee_id"]) && strlen($updateData["employee_id"])) {
            $setFields .= "employee_id=?,";
            $format .= "s";
            array_push($setParams, $updateData["employee_id"]);
        }
        if(isset($updateData["issue_date"]) && strlen($updateData["issue_date"])) {
            $setFields .= "issue_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["issue_date"]);
        }
        if(isset($updateData["role"]) && strlen($updateData["role"])) {
            $setFields .= "role=?,";
            $format .= "s";
            array_push($setParams, $updateData["role"]);
        }
        if(isset($updateData["amount"]) && strlen($updateData["amount"])) {
            $setFields .= "amount=?,";
            $format .= "d";
            array_push($setParams, $updateData["amount"]);
        }
        if(isset($updateData["type"]) && strlen($updateData["type"])) {
            $setFields .= "type=?,";
            $format .= "s";
            array_push($setParams, $updateData["type"]);
        }
        if(isset($updateData["project"]) && strlen($updateData["project"])) {
            $setFields .= "project=?,";
            $format .= "s";
            array_push($setParams, $updateData["project"]);
        }
        if(isset($updateData["project_value"]) && strlen($updateData["project_value"])) {
            $setFields .= "project_value=?,";
            $format .= "d";
            array_push($setParams, $updateData["project_value"]);
        }
        if(isset($updateData["percent"]) && strlen($updateData["percent"])) {
            $setFields .= "percent=?,";
            $format .= "d";
            array_push($setParams, $updateData["percent"]);
        }
        if(isset($updateData["status"]) && strlen($updateData["status"])) {
            $setFields .= "status=?,";
            $format .= "s";
            array_push($setParams, $updateData["status"]);
        }
        if(isset($updateData["approved_by"]) && strlen($updateData["approved_by"])) {
            $setFields .= "approved_by=?,";
            $format .= "s";
            array_push($setParams, $updateData["approved_by"]);
        }
        if(isset($updateData["approve_date"]) && strlen($updateData["approve_date"])) {
            $setFields .= "approve_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["approve_date"]);
        }
        if(isset($updateData["approval_note"]) && strlen($updateData["approval_note"])) {
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


        $query = "UPDATE commissions SET ".$setFields.$queryOptions["condition"];
        $queryOptions["params"][0] = $format . $queryOptions["params"][0];
        $updateParams = Array($queryOptions["params"][0]);
        $updateParams = array_merge($updateParams, $setParams);
        array_shift($queryOptions["params"]);
        $updateParams = array_merge($updateParams, $queryOptions["params"]);
        // echo "\n----- $query -----\n";

        return $this->update($query, $updateParams);
    }
}
?>