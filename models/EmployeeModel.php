<?php
require_once PROJECT_ROOT_PATH . "/models/Database.php";
class EmployeeModel extends Database{

    public function addEmployee($company, $reqParams){
        // print_r($reqParams);
        $query = "INSERT INTO employees (".
        "company, employee_id, fullname, fullname_en, mobile_no, birthdate, id_number, gender, ".
        // "enroll_date, position, department, salary, employ_type, payment_type, bank, bank_acc_no, ".
        "enroll_date, employ_type, payment_type, bank, bank_acc_no, ".
        "username, email, ".
        "contact_no, contact_person, relation, ".
        "documents, ".
        "address".
        ") VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->insert($query, 
            Array("ssssssssssssssssssss", 
            $company, $reqParams["employee_id"], $reqParams["fullname"], $reqParams["fullname_en"], $reqParams["mobile_no"], 
            $reqParams["birthdate"], $reqParams["id_number"], $reqParams["gender"], 
            
            // $reqParams["enroll_date"], $reqParams["position"], $reqParams["department"], $reqParams["salary"], 
            $reqParams["enroll_date"], 
            $reqParams["employ_type"], $reqParams["payment_type"], $reqParams["bank"], $reqParams["bank_acc_no"], 
            $reqParams["username"], $reqParams["email"],

            $reqParams["contact_no"], $reqParams["contact_person"], $reqParams["relation"],
            $reqParams["documents"],
            $reqParams["address"] 
        ));
    }

    public function addSalary($company, $employeeId, $salary, $department, $position, $effectiveDate, $note, $status)
    {

        $query = "INSERT INTO work_history " .
            "(company, employee_id, salary, department, position, effective_date, note, status)" .
            "VALUES (?, ?, ?, ?, ?, ?, ?, ?)";


        return $this->insert($query, array(
            "ssdsssss",
            $company, $employeeId, $salary, $department, $position, $effectiveDate, $note, $status
        ));
    }
    
    public function getCurrentSalary($company, $employeeId, $status) {
        // WHERE company="OWLS" AND employee_id="OWLS0003" AND status="APPROVED"
        $statusParam = (isset($status) && strlen($status));
        $query = "SELECT * FROM `work_history` 
        WHERE company=? AND employee_id=? ".($statusParam?"AND status=?":"").
        "ORDER BY effective_date DESC
        LIMIT 1";

        $quryParams = Array("ss".($statusParam?"s":""), $company, $employeeId);
        if($statusParam) {
            array_push($quryParams, $status);
        }

        $result = $this->select($query, $quryParams);
        if($result && $result[0]) {
            return $result[0];
        }
        return false;
    }

    public function getSalaryHist($company, $reqParams)
    {

        if($company) {
            array_push($reqParams, Array("company" => $company));
        }

        $mapFieldType = Array(
            "t1.rec_id"=>Array("i", ""),
            "t1.company"=>Array("s", "upper"),
            "t1.employee_id"=>Array("s", "upper"),
            "t1.department"=>Array("s", ""),
            "t1.status"=>Array("s", "upper"),
        );


        // $mapFieldType = Array(
        //     "t1.company"=>Array("s", "upper"),
        //     "t1.username"=>Array("s", "upper"),
        //     "t1.email"=>Array("s", "lower"),
        //     "t1.employee_id"=>Array("s", "upper"),
        //     "t1.id_number"=>Array("s", ""),
        //     "t1.role"=>Array("s", "upper"),
        // );
        $condition = Array();
        foreach($reqParams as $group){
            $newGroup = Array();
            foreach($group as $key=>$value){
               $newGroup["t1.".$key] = $value;
            }
            array_push($condition, $newGroup);
        }


        $sort = isset($reqParams["sort"]) ? $reqParams["sort"] : "";

        $queryOptions = getQueryOptions($mapFieldType, $condition);


        $query = "SELECT t1.*, t2.fullname FROM work_history as t1
        LEFT JOIN employees t2
        ON t1.company=t2.company AND t1.employee_id=t2.employee_id ".
        $queryOptions["condition"]." ORDER BY t1.employee_id, effective_date DESC";

        // echo "\n\n";
        // print_r($queryOptions["condition"]);
        // echo "\n\n";

        return $this->select($query, $queryOptions["params"], $sort);
    }

    // Called by update employee
    public function updateSalaryHist($company, $employeeId, $position, $department, $salary) {
        $query = "UPDATE work_history
        SET position=?, department=?, salary=?	 
        WHERE company=? AND employee_id=? AND status<>'DENIED'
        ORDER BY effective_date DESC 
        LIMIT 1";
        echo "$query, $position, $department $salary, $company, $employeeId";
        return $this->update($query, Array("ssdss", $position, $department, $salary, $company, $employeeId));
    }

    public function updateSalary($recId, $updateData){

        // $formatParam = "";
        $setFields = "";
        $prepareParams = Array("");

        if(isset($updateData["effectiveDate"])) {
            $prepareParams[0] .= "s";
            $setFields .= "effective_date=?,";
            array_push($prepareParams, $updateData["effectiveDate"]);
        }

        if(isset($updateData["department"])) {
            $prepareParams[0] .= "s";
            $setFields .= "department=?,";
            array_push($prepareParams, $updateData["department"]);
        }

        if(isset($updateData["position"])) {
            $prepareParams[0] .= "s";
            $setFields .= "position=?,";
            array_push($prepareParams, $updateData["position"]);
        }

        if(isset($updateData["salary"])) {
            $prepareParams[0] .= "d";
            $setFields .= "salary=?,";
            array_push($prepareParams, $updateData["salary"]);
        }

        if(isset($updateData["status"])) {
            $prepareParams[0] .= "s";
            $setFields .= "status=?,";
            array_push($prepareParams, $updateData["status"]);
        }

        if(isset($updateData["note"])) {
            $prepareParams[0] .= "s";
            $setFields .= "note=?,";
            array_push($prepareParams, $updateData["note"]);
        }

        $setFields .= "last_update=NOW()";
        $prepareParams[0] .= "s";
        array_push($prepareParams, $recId);

        // $setFields = rtrim($setFields, ",");

        $query = "UPDATE work_history 
        SET $setFields 
        WHERE rec_id=?";

        // echo "$query";
        // print_r($prepareParams);

        return $this->update($query, $prepareParams);
    }

    public function getEmployees0($company, $reqParams, $limit=0){
        if($company) {
            array_push($reqParams["condition"], Array("company" => $company));
        }
        // $reqParams["condition"][0]["company"] = $company;
        $mapFieldType = Array(
            "company"=>Array("s", "upper"),
            "username"=>Array("s", "upper"),
            "email"=>Array("s", "lower"),
            "employee_id"=>Array("s", "upper"),
            "id_number"=>Array("s", ""),
            "role"=>Array("s", "upper"),
        );
        $sort = isset($reqParams["sort"]) ? $reqParams["sort"] : "";
        // echo "\n\n";
        // print_r($reqParams);
        // echo "\n\n";

        $queryOptions = getQueryOptions($mapFieldType, $reqParams["condition"]);
        // print_r($queryOptions);
        // echo "\n---> ".$queryOptions["condition"]."---\n";
        return $this->select("SELECT * FROM employees ".$queryOptions["condition"], $queryOptions["params"], $sort);
    }

    public function getEmployees1($company, $reqParams, $limit=0){
        if($company) {
            array_push($reqParams["condition"], Array("company" => $company));
        }
        // $reqParams["condition"][0]["company"] = $company;
        $mapFieldType = Array(
            "t1.company"=>Array("s", "upper"),
            "t1.username"=>Array("s", "upper"),
            "t1.email"=>Array("s", "lower"),
            "t1.employee_id"=>Array("s", "upper"),
            "t1.id_number"=>Array("s", ""),
            "t1.role"=>Array("s", "upper"),
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
        $queryOptions = getQueryOptions($mapFieldType, $condition);
        $query = "SELECT t1.*, t2.username as uname, t2.reset, t3.role  
        FROM employees as t1
        LEFT JOIN users t2
        ON t1.company=t2.company AND t1.employee_id=t2.employee_id
        LEFT JOIN user_roles as t3
        ON t2.username=t3.username
        -- LEFT JOIN work_history t4
        -- ON t1.company=t4.company AND t1.employee_id=t4.employee_id
        ".$queryOptions["condition"];

        // print_r($queryOptions);
        // echo "\n---> ".$queryOptions["condition"]."---\n";
        return $this->select($query, $queryOptions["params"], $sort);
    }

    public function getEmployees($company, $reqParams, $limit=0){
        if($company) {
            array_push($reqParams["condition"], Array("company" => $company));
        }
        // $reqParams["condition"][0]["company"] = $company;
        $mapFieldType = Array(
            "t1.company"=>Array("s", "upper"),
            "t1.username"=>Array("s", "upper"),
            "t1.email"=>Array("s", "lower"),
            "t1.employee_id"=>Array("s", "upper"),
            "t1.id_number"=>Array("s", ""),
            "t1.role"=>Array("s", "upper"),
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
        $queryOptions = getQueryOptions($mapFieldType, $condition);
        // $query = "SELECT t1.*, t2.username as uname, t2.reset, t3.role 
        $query = "SELECT t1.*, t2.username as uname, t2.reset, t3.role, t4.rec_id as salary_ref_id, t4.salary, t4.effective_date as salary_effective_date, t4.department, t4.position  
        FROM employees as t1
        LEFT JOIN users t2
        ON t1.company=t2.company AND t1.employee_id=t2.employee_id
        LEFT JOIN user_roles as t3
        ON t2.username=t3.username
        LEFT JOIN work_history t4
        ON t1.company=t4.company AND t1.employee_id=t4.employee_id AND t4.status<>'DENIED' AND
            t4.effective_date = (SELECT MAX(effective_date) FROM work_history as t5 WHERE t1.company=t5.company AND t1.employee_id=t5.employee_id AND status<>'DENIED')
        ".$queryOptions["condition"];

        // print_r($queryOptions);
        // echo "\n---> ".$queryOptions["condition"]."---\n";
        return $this->select($query, $queryOptions["params"], $sort);
    }
    
    public function getNewEmployeeId($company) {
        $idLength = 4;
        $query = "SELECT MAX(employee_id) as employee_id FROM employees WHERE employee_id LIKE ?";
        $result = $this->select($query, Array("s", "$company%"));
        if($result && sizeof($result)) {
            $newId = $company.str_pad(substr($result[0]["employee_id"], strlen($company)) + 1, $idLength, "0", STR_PAD_LEFT);
        } else {
            $newId = $company.str_pad(1, $idLength, "0", STR_PAD_LEFT);
        }
        return $newId;
    }

    public function updateEmployee($company, $reqCondition, $updateData){
        if($company) {
            array_push($reqCondition, Array("company" => $company));
        }

        $mapFieldType = Array(
            "company"=>Array("s", "upper"),
            "employee_id"=>Array("s", "upper"),
            "username"=>Array("s", "upper"),
            "rec_id"=>Array("i", ""),

            "email"=>Array("s", ""),
            "fullname"=>Array("s", ""),
            "gender"=>Array("s", ""),
            "birthdate"=>Array("s", ""),
            "enrollDate"=>Array("s", ""),
            "contactNo"=>Array("s", ""),
            "contactPerson"=>Array("s", ""),
            "role"=>Array("s", ""),
            "department"=>Array("s", ""),
    
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
        // if(isset($updateData["employeeId"]) && strlen($updateData["employeeId"])) {
        //     $setFields .= "employee_id=?,";
        //     $format .= "s";
        //     array_push($setParams, $updateData["employeeId"]);
        // }
        // if(isset($updateData["username"]) && strlen($updateData["username"])) {
        //     $setFields .= "username=?,";
        //     $format .= "s";
        //     array_push($setParams, $updateData["username"]);
        // }

        if(isset($updateData["fullname"]) && strlen($updateData["fullname"])) {
            $setFields .= "fullname=?,";
            $format .= "s";
            array_push($setParams, $updateData["fullname"]);
        }
        if(isset($updateData["fullnameEn"]) && strlen($updateData["fullnameEn"])) {
            $setFields .= "fullname_en=?,";
            $format .= "s";
            array_push($setParams, $updateData["fullnameEn"]);
        }
        if(isset($updateData["gender"]) && strlen($updateData["gender"])) {
            $setFields .= "gender=?,";
            $format .= "s";
            array_push($setParams, $updateData["gender"]);
        }
        if(isset($updateData["birthdate"]) && strlen($updateData["birthdate"])) {
            $setFields .= "birthdate=?,";
            $format .= "s";
            array_push($setParams, $updateData["birthdate"]);
        }
        if(isset($updateData["idNumber"]) && strlen($updateData["idNumber"])) {
            $setFields .= "id_number=?,";
            $format .= "s";
            array_push($setParams, $updateData["idNumber"]);
        }
        if(isset($updateData["mobileNo"]) && strlen($updateData["mobileNo"])) {
            $setFields .= "mobile_no=?,";
            $format .= "s";
            array_push($setParams, $updateData["mobileNo"]);
        }

        if(isset($updateData["email"]) && strlen($updateData["email"])) {
            $setFields .= "email=?,";
            $format .= "s";
            array_push($setParams, $updateData["email"]);
        }

        // "salary"=>Array("number", "optional"),
        // "paymentType"=>Array("string", "optional"),
        // "bank"=>Array("string", "optional"),
        // "bankAccNo"=>Array("string", "optional"),

        // "role"=>Array("string", "optional"),


        if(isset($updateData["enrollDate"]) && strlen($updateData["enrollDate"])) {
            $setFields .= "enroll_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["enrollDate"]);
        }

        // Move to update in TBL work_history
        // if(isset($updateData["position"]) && strlen($updateData["position"])) {
        //     $setFields .= "position=?,";
        //     $format .= "s";
        //     array_push($setParams, $updateData["position"]);
        // }
        // if(isset($updateData["department"]) && strlen($updateData["department"])) {
        //     $setFields .= "department=?,";
        //     $format .= "s";
        //     array_push($setParams, $updateData["department"]);
        // }

        if(isset($updateData["employType"]) && strlen($updateData["employType"])) {
            $setFields .= "employ_type=?,";
            $format .= "s";
            array_push($setParams, $updateData["employType"]);
        }


        if(isset($updateData["contactNo"]) && strlen($updateData["contactNo"])) {
            $setFields .= "contact_no=?,";
            $format .= "s";
            array_push($setParams, $updateData["contactNo"]);
        }
        if(isset($updateData["contactPerson"]) && strlen($updateData["contactPerson"])) {
            $setFields .= "contact_person=?,";
            $format .= "s";
            array_push($setParams, $updateData["contactPerson"]);
        }
        if(isset($updateData["relation"]) && strlen($updateData["relation"])) {
            $setFields .= "relation=?,";
            $format .= "s";
            array_push($setParams, $updateData["relation"]);
        }

        if(isset($updateData["documents"]) && strlen($updateData["documents"])) {
            $setFields .= "documents=?,";
            $format .= "s";
            array_push($setParams, $updateData["documents"]);
        }

        $setFields = rtrim($setFields, ",");
        $queryOptions = getQueryOptions($mapFieldType, $condition);

        // echo "Start Model\n".$query."\n";
        // print_r($queryOptions);
        // echo "\nEnd Model\n";

        $query = "UPDATE employees SET ".$setFields.$queryOptions["condition"];
        $queryOptions["params"][0] = $format . $queryOptions["params"][0];
        $updateParams = Array($queryOptions["params"][0]);
        $updateParams = array_merge($updateParams, $setParams);
        array_shift($queryOptions["params"]);
        $updateParams = array_merge($updateParams, $queryOptions["params"]);


        return $this->update($query, $updateParams);
    }
}
?>