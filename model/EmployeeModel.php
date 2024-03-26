<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class EmployeeModel extends Database{

    public function addEmployee($company, $reqParams){
        print_r($reqParams);
        $query = "INSERT INTO ".strtolower($company)."_employees ".
        "(employee_id, username, email, fullname, fullname_en, id_card_no, gender, birthdate, address, enroll_date, contact_no, contact_person, role, department)".
        "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->insert($query, 
            Array("ssssssisssssss", 
            $reqParams["employee_id"], $reqParams["username"], $reqParams["email"], $reqParams["fullname"], $reqParams["fullname_en"],
            $reqParams["id_card_no"], $reqParams["gender"], $reqParams["birthdate"], $reqParams["address"], $reqParams["enroll_date"], 
            $reqParams["contact_no"], $reqParams["contact_person"], $reqParams["role"], $reqParams["department"]
        ));
    }

    public function getEmployees($company, $reqParams, $limit=0){
        $mapFileType = Array(
            "username"=>Array("s", "upper"),
            "email"=>Array("s", "lower"),
            "employee_id"=>Array("s", "upper"),
            "role"=>Array("s", "upper"),
        );
        $queryOptions = getQueryOptions($mapFileType, $reqParams["condition"]);
        // echo "\n---> ".$queryOptions["condition"]."---\n";
        return $this->select("SELECT * FROM ".strtolower($company)."_employees ".$queryOptions["condition"], $queryOptions["params"], $reqParams["sort"]);
    }
    
    public function updateEmployee($condParams, $updateData){
        // $query = "UPDATE employees SET ".
        // "username=?, email=?, fullname=?, gender=?, birthdate=?, ".
        // "enroll_date=?, contact_no=?, contact_person=?, role=?, department=?".
        // " WHERE employee_id=?";
        // return $this->update($query, 
        //     Array("sssisssssss", 
        //     $reqParams["username"], $reqParams["email"], $reqParams["fullname"],
        //     $reqParams["gender"], $reqParams["birthdate"], $reqParams["enroll_date"], $reqParams["contact_no"],
        //     $reqParams["contact_person"], $reqParams["role"], $reqParams["department"], $reqParams["employee_id"]
        // ));
        $mapFieldType = Array(
            "username"=>Array("s", "upper"),
            "employee_id"=>Array("s", "upper"),
            // "company"=>Array("s", "upper"),
        );
        // echo "\n----- condParams -----\n";
        // print_r($condParams);
        // echo "\n----- updateData -----\n";
        // print_r($updateData);
        $setParams = Array();
        $setFields = "";
        $format = "";
        if(isset($updateData["employee_id"]) && strlen($updateData["employee_id"])) {
            $setFields .= "employee_id=?,";
            $format .= "s";
            array_push($setParams, $updateData["employee_id"]);
        }
        if(isset($updateData["username"]) && strlen($updateData["username"])) {
            $setFields .= "username=?,";
            $format .= "s";
            array_push($setParams, $updateData["username"]);
        }
        if(isset($updateData["email"]) && strlen($updateData["email"])) {
            $setFields .= "email=?,";
            $format .= "s";
            array_push($setParams, $updateData["email"]);
        }
        if(isset($updateData["fullname"]) && strlen($updateData["fullname"])) {
            $setFields .= "fullname=?,";
            $format .= "s";
            array_push($setParams, $updateData["fullname"]);
        }
        if(isset($updateData["gender"]) && strlen($updateData["gender"])) {
            $setFields .= "gender=?,";
            $format .= "i";
            array_push($setParams, $updateData["gender"]);
        }
        if(isset($updateData["birthdate"]) && strlen($updateData["birthdate"])) {
            $setFields .= "birthdate=?,";
            $format .= "s";
            array_push($setParams, $updateData["birthdate"]);
        }
        if(isset($updateData["enroll_date"]) && strlen($updateData["enroll_date"])) {
            $setFields .= "enroll_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["enroll_date"]);
        }
        if(isset($updateData["contact_no"]) && strlen($updateData["contact_no"])) {
            $setFields .= "contact_no=?,";
            $format .= "s";
            array_push($setParams, $updateData["contact_no"]);
        }
        if(isset($updateData["contact_person"]) && strlen($updateData["contact_person"])) {
            $setFields .= "contact_person=?,";
            $format .= "s";
            array_push($setParams, $updateData["contact_person"]);
        }
        if(isset($updateData["role"]) && strlen($updateData["role"])) {
            $setFields .= "role=?,";
            $format .= "s";
            array_push($setParams, $updateData["role"]);
        }
        if(isset($updateData["department"]) && strlen($updateData["department"])) {
            $setFields .= "department=?,";
            $format .= "s";
            array_push($setParams, $updateData["department"]);
        }

        $setFields = rtrim($setFields, ",");
        $queryOptions = getQueryOptions($mapFieldType, $condParams);
        // echo "\n----- condParams -----\n";
        // print_r($condParams);


        $query = "UPDATE employees SET ".$setFields.$queryOptions["condition"];

        $format .= $queryOptions["params"][0];
        array_unshift($setParams, $format);
        array_shift($queryOptions["params"]);
        $setParams = array_merge($setParams, $queryOptions["params"]); 

        // echo "\nquery:\n". $query ."\n-----\n";

        // echo "\n----- setParams -----\n";
        // print_r($setParams);

        return $this->update($query, $setParams);
    }
}
?>