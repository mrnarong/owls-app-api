<?php
require_once PROJECT_ROOT_PATH . "/models/Database.php";
class ExpenseModel extends Database{

    public function addExpense($company, $reqParams){
        $query = "INSERT INTO expenses ".
        "(company, employee_id, item_date, detail, amount, documents)".
        "VALUES (?, ?, ?, ?, ?, ?)";


        return $this->insert($query, Array(
            "ssssds",
            $company, $reqParams["employee_id"], $reqParams["item_date"], $reqParams["detail"],$reqParams["amount"], $reqParams["documents"]
        ));
    }

    public function getExpenses($company, $reqParams, $limit=0){
        // print_r($reqParams);
        if($company) {
            array_push($reqParams["condition"], Array("company" => $company));
        }
        $mapFileType = Array(
            "t1.company"=>Array("s", "upper"),
            // "username"=>Array("s", "upper"),
            // "email"=>Array("s", "lower"),
            "t1.employee_id"=>Array("s", "upper"),
            "t1.item_date"=>Array("s", "regex"),
            "t1.status"=>Array("s", "upper"),
            );

        $condition = Array();
        foreach($reqParams["condition"] as $group){
            $newGroup = Array();
            foreach($group as $key=>$value){
                // echo "\n".$key."\n";
                // print_r($value);
               $newGroup["t1.".$key] = $value;
            }
            array_push($condition, $newGroup);
        }

        // print_r($condition);

        // $sort = isset($reqParams["sort"]) ? $reqParams["sort"] : "";
        $sortOption = " ORDER BY t1.company, t2.fullname, t1.item_date";
        $queryOptions = getQueryOptions($mapFileType, $condition);// $reqParams["condition"]);
        
        $query = "SELECT t1.*, t1.approval_note as approve_note, t2.fullname as fullname 
        FROM expenses as t1 ".
        "LEFT JOIN employees t2 
        ON t1.company=t2.company AND t1.employee_id=t2.employee_id".
        $queryOptions["condition"].$sortOption;

        // echo $query."\n";
        // print_r($queryOptions);

        // return $this->select("SELECT * FROM expenses ".$queryOptions["condition"], $queryOptions["params"], $sort);
        return $this->select($query, $queryOptions["params"], []);
    }
    
    public function updateExpense($condParams, $updateData){
        $mapFieldType = Array(
            "rec_id"=>Array("i", "upper"),
            "company"=>Array("s", "upper"),
            "username"=>Array("s", "upper"),
            "employee_id"=>Array("s", "upper"),
            "item_date"=>Array("s", ""),
            "detail"=>Array("s", ""),
            "amount"=>Array("d", ""),
            "documents"=>Array("s", ""),
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
        if(isset($updateData["item_date"]) && strlen($updateData["item_date"])) {
            $setFields .= "item_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["item_date"]);
        }
        if(isset($updateData["detail"]) && strlen($updateData["detail"])) {
            $setFields .= "detail=?,";
            $format .= "s";
            array_push($setParams, $updateData["detail"]);
        }
        if(isset($updateData["amount"]) && strlen($updateData["amount"])) {
            $setFields .= "amount=?,";
            $format .= "d";
            array_push($setParams, $updateData["amount"]);
        }
        if(isset($updateData["documents"]) && strlen($updateData["documents"])) {
            $setFields .= "documents=?,";
            $format .= "s";
            array_push($setParams, $updateData["documents"]);
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


        $query = "UPDATE expenses SET ".$setFields.$queryOptions["condition"];
        $queryOptions["params"][0] = $format . $queryOptions["params"][0];
        $updateParams = Array($queryOptions["params"][0]);
        $updateParams = array_merge($updateParams, $setParams);
        array_shift($queryOptions["params"]);
        $updateParams = array_merge($updateParams, $queryOptions["params"]);

        return $this->update($query, $updateParams);
    }
}
?>