<?php
require_once PROJECT_ROOT_PATH . "/models/Database.php";
class OvertimeModel extends Database{

    public function addOvertime($company, $reqParams){
        $query = "INSERT INTO overtimes ".
        "(company, employee_id, project, detail, start_datetime, end_datetime)".
        "VALUES (?, ?, ?, ?, ?, ?)";

        return $this->insert($query, Array(
            "ssssss",
            $company, $reqParams["employee_id"], $reqParams["project"], $reqParams["detail"], $reqParams["start_datetime"],$reqParams["end_datetime"]
        ));
    }

    // public function getOvertimes($company, $reqParams, $limit=0){
    //     if($company) {
    //         array_push($reqParams["condition"], Array("company" => $company));
    //     }
    //     $mapFileType = Array(
    //         "company"=>Array("s", "upper"),
    //         // "username"=>Array("s", "upper"),
    //         // "email"=>Array("s", "lower"),
    //         "employee_id"=>Array("s", "upper"),
    //         "start_datetime"=>Array("s", "regex"),
    //         "status"=>Array("s", "lower"),
    //         );
    //     $sort = isset($reqParams["sort"]) ? $reqParams["sort"] : "";
    //     // print_r($reqParams);
    //     $queryOptions = getQueryOptions($mapFileType, $reqParams["condition"]);
        
    //     // print_r($queryOptions);
    //     // echo "\n---> ".$queryOptions["condition"]."---\n";
    //     return $this->select("SELECT * FROM overtimes ".$queryOptions["condition"], $queryOptions["params"], $sort);
    // }

    public function getOvertimes($company, $reqParams, $limit=0){
        if($company) {
            array_push($reqParams["condition"], Array("company" => $company));
        }
        $mapFileType = Array(
            "overtimes.company"=>Array("s", "upper"),
            // "username"=>Array("s", "upper"),
            // "email"=>Array("s", "lower"),
            "overtimes.employee_id"=>Array("s", "upper"),
            "overtimes.start_datetime"=>Array("s", "regex"),
            "overtimes.status"=>Array("s", "upper"),
            );

        $condition = Array();
        foreach($reqParams["condition"] as $group){
            $newGroup = Array();
            foreach($group as $key=>$value){
                // echo "\n".$key."\n";
                // print_r($value);
               $newGroup["overtimes.".$key] = $value;
            }
            array_push($condition, $newGroup);
        }

        // print_r($condition);

        $sort = isset($reqParams["sort"]) ? $reqParams["sort"] : "";
        $queryOptions = getQueryOptions($mapFileType, $condition);// $reqParams["condition"]);
        
        $query = "SELECT overtimes.*, overtimes.approval_note as approve_note, employees.fullname as fullname FROM overtimes ".
        "LEFT JOIN employees ON overtimes.employee_id=employees.employee_id".
        $queryOptions["condition"];

        // print_r($queryOptions);

        // return $this->select("SELECT * FROM expenses ".$queryOptions["condition"], $queryOptions["params"], $sort);
        return $this->select($query, $queryOptions["params"], $sort);
    }

    public function updateOvertime($condParams, $updateData){
        $mapFieldType = Array(
            "rec_id"=>Array("i", "upper"),
            "company"=>Array("s", "upper"),
            "username"=>Array("s", "upper"),
            "employee_id"=>Array("s", "upper"),
            "start_date"=>Array("s", ""),
            "end_date"=>Array("s", ""),
            "project"=>Array("s", ""),
            "detail"=>Array("s", ""),
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
        if(isset($updateData["start_date"]) && strlen($updateData["start_date"])) {
            $setFields .= "start_datetime=?,";
            $format .= "s";
            array_push($setParams, $updateData["start_date"]);
        }
        if(isset($updateData["end_date"]) && strlen($updateData["end_date"])) {
            $setFields .= "end_datetime=?,";
            $format .= "s";
            array_push($setParams, $updateData["end_date"]);
        }
        if(isset($updateData["project"]) && strlen($updateData["project"])) {
            $setFields .= "project=?,";
            $format .= "s";
            array_push($setParams, $updateData["project"]);
        }
        if(isset($updateData["detail"]) && strlen($updateData["detail"])) {
            $setFields .= "detail=?,";
            $format .= "s";
            array_push($setParams, $updateData["detail"]);
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


        $query = "UPDATE overtimes SET ".$setFields.$queryOptions["condition"];
        $queryOptions["params"][0] = $format . $queryOptions["params"][0];
        $updateParams = Array($queryOptions["params"][0]);
        $updateParams = array_merge($updateParams, $setParams);
        array_shift($queryOptions["params"]);
        $updateParams = array_merge($updateParams, $queryOptions["params"]);

        return $this->update($query, $updateParams);
    }
}
?>