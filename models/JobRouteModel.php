<?php
require_once PROJECT_ROOT_PATH . "/models/Database.php";
class JobRouteModel extends Database{

    public function addJobRoute($company, $reqParams){
        // print_r($reqParams);
// rec_id 	employee_id 	type 	leave_reason 	issue_date 	start_date 	end_date 	approve_date 	approve_status 	approve_reason 	remark 
        $query = "INSERT INTO job_routes ".
        "(company, employee_id, project, routing_date, origin_place, origin_lat, origin_lng, dest_place, dest_lat, dest_lng, distance, photo)".
        "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->insert($query, 
            Array("ssssssssssss", 
            $company, $reqParams["employee_id"], $reqParams["project"], $reqParams["routing_date"], 
            mb_substr($reqParams["origin_place"], 0, 120, 'UTF-8'), $reqParams["origin_lat"], $reqParams["origin_lng"],
            mb_substr($reqParams["dest_place"], 0, 120, 'UTF-8'), $reqParams["dest_lat"], $reqParams["dest_lng"],
            // $reqParams["origin_place"], $reqParams["origin_lat"], $reqParams["origin_lng"],
            // $reqParams["dest_place"], $reqParams["dest_lat"], $reqParams["dest_lng"],
            $reqParams["distance"], $reqParams["documents"]
        ));
    }

    public function getJobRoutes($company, $reqParams, $limit=0){
        // $reqParams["condition"][0]["company"] = $company;
        if($company) {
            array_push($reqParams["condition"], Array("company" => $company));
        }
        $mapFieldType = Array(
            "t1.company"=>Array("s", "upper"),
            "t1.employee_id"=>Array("s", "upper"),
            "t1.routing_date"=>Array("s", ""),
            "t1.status"=>Array("s", "upper"),
        );
        // print_r($reqParams["condition"]);

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
        $sortOption = " ORDER BY t1.company, t2.fullname, t1.routing_date";
        $queryOptions = getQueryOptions($mapFieldType, $reqParams["condition"]);
        $sort = []; //isset($condition["sort"]) ? $reqParams["sort"] : [];

        $query = "SELECT t1.*, t2.fullname as fullname 
        FROM job_routes as t1 ".
        "LEFT JOIN employees t2 
        ON t1.company=t2.company AND t1.employee_id=t2.employee_id".
        $queryOptions["condition"].$sortOption;

        // print_r($queryOptions);

        return $this->select($query, $queryOptions["params"], []);
        // // print_r($sort);
        // return $this->select("SELECT * FROM job_routes ".$queryOptions["condition"], $queryOptions["params"], $sort);
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

    public function updateJobRoute($company, $reqCondition, $updateData){
        if($company) {
            array_push($reqCondition, Array("company" => $company));
        }

        $mapFieldType = Array(
            "company"=>Array("s", "upper"),
            "rec_id"=>Array("i", ""),
            "employee_id"=>Array("s", "upper"),
            "routing_date"=>Array("s", ""),
            "origin_place"=>Array("s", ""),
            "origin_lat"=>Array("d", ""),
            "origin_lng"=>Array("d", ""),
            "destP_place"=>Array("s", ""),
            "dest_lat"=>Array("d", ""),
            "dest_lng"=>Array("d", ""),
            "distance"=>Array("d", ""),
            "status"=>Array("s", "upper"),
        );

        // "condition": [
        //     // {"recId": 1, "test": 2},
        //     {"employeeId": "OWL0001"}
        // ],
        $condition = Array();
        foreach($reqCondition as $group){
            $newGroup = Array();
            // echo $group." => ".$gValue;

            foreach($group as $key=>$value){
                // echo $key."\n".$value."\n";
                $newGroup[$key] = $value;
            }
            array_push($condition, $newGroup);
        }


        // $updateData = Array();
        // foreach($reqUpdateData as $group){
        //     $newGroup = Array();
        //     foreach($group as $key=>$value){
        //         // echo "\n".$value."\n";
        //        $newGroup[$key] = $value;
        //     }
        //     array_push($updateData, $newGroup);
        // }

        // // print_r($updateData);
        $setParams = Array();
        $setFields = "";
        $format = "";

        if(isset($updateData["employeeId"]) && strlen($updateData["employeeId"])) {
            $setFields .= "employee_id=?,";
            $format .= "s";
            array_push($setParams, $updateData["employeeId"]);
        }
        if(isset($updateData["routingDate"]) && strlen($updateData["routingDate"])) {
            $setFields .= "routing_date=?,";
            $format .= "s";
            array_push($setParams, $updateData["routingDate"]);
        }
        if(isset($updateData["originPlace"]) && strlen($updateData["originPlace"])) {
            $setFields .= "origin_place=?,";
            $format .= "s";
            array_push($setParams, $updateData["originPlace"]);
        }
        if(isset($updateData["originLat"])) {
            $setFields .= "origin_lat=?,";
            $format .= "d";
            array_push($setParams, $updateData["originLat"]);
        }
        if(isset($updateData["originLng"])) {
            $setFields .= "origin_lng=?,";
            $format .= "d";
            array_push($setParams, $updateData["originLng"]);
        }
        if(isset($updateData["destPlace"]) && strlen($updateData["destPlace"])) {
            $setFields .= "dest_place=?,";
            $format .= "s";
            array_push($setParams, $updateData["destPlace"]);
        }
        if(isset($updateData["destLat"])) {
            $setFields .= "dest_lat=?,";
            $format .= "d";
            array_push($setParams, $updateData["destLat"]);
        }
        if(isset($updateData["destLng"])) {
            $setFields .= "dest_lng=?,";
            $format .= "d";
            array_push($setParams, $updateData["destLng"]);
        }
        if(isset($updateData["distance"])) {
            $setFields .= "distance=?,";
            $format .= "d";
            array_push($setParams, $updateData["distance"]);
        }
        if(isset($updateData["status"]) && strlen($updateData["status"])) {
            $setFields .= "status=?,";
            $format .= "s";
            array_push($setParams, $updateData["status"]);
        }
        if(isset($updateData["approveNote"]) && strlen($updateData["approveNote"])) {
            $setFields .= "approve_note=?,";
            $format .= "s";
            array_push($setParams, $updateData["approveNote"]);
        }
        if(isset($updateData["approvedBy"]) && strlen($updateData["approvedBy"])) {
            $setFields .= "approved_by=?,";
            $format .= "s";
            array_push($setParams, $updateData["approvedBy"]);
        }

        if(isset($updateData["approveDate"]) && strlen($updateData["approveDate"])) {
            $setFields .= "approve_date=?,";
            $format .= "s";
            // array_push($setParams, date("Y-m-d H:i:s"));
            array_push($setParams, $updateData["approveDate"]);
        }

        // if(isset($updateData["remark"]) && strlen($updateData["remark"])) {
        //     $setFields .= "remark=?,";
        //     $format .= "s";
        //     array_push($setParams, $updateData["remark"]);
        // }

        // approver, approval_note, approval_date
        


        $setFields = rtrim($setFields, ",");
        $queryOptions = getQueryOptions($mapFieldType, $condition);
        // echo "Start Model\n";
        // print_r($queryOptions);
        // echo "\nEnd Model\n";


        $query = "UPDATE job_routes SET ".$setFields.$queryOptions["condition"]; // . " WHERE company=? AND recId ";
        $queryOptions["params"][0] = $format . $queryOptions["params"][0];
        $updateParams = Array($queryOptions["params"][0]); // array_merge($setParams, $queryOptions["params"]); 
        $updateParams = array_merge($updateParams, $setParams);//, array_shift($queryOptions["params"]));
        array_shift($queryOptions["params"]);
        $updateParams = array_merge($updateParams, $queryOptions["params"]);

        // echo "Start Model\n".$query."\n";
        // print_r($updateParams);
        // echo "\nEnd Model\n";

        return $this->update($query, $updateParams);
    }
}
?>