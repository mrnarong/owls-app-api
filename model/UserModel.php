<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class UserModel extends Database
{
    public function addActivateUser($reqParams){
        $query = "INSERT INTO activate_users ".
        "(token, user_email, create_time, valid_time )".
        "VALUES (?, ?, NOW(), DATE_ADD( NOW(), INTERVAL 24 HOUR ) )";
        return $this->insert($query, 
            Array("ss", 
                $reqParams["token"], $reqParams["user_email"]
            )
        );
    }

    public function setActivateStatus($reqParams){
        $query = "UPDATE activate_users SET ".
        "status=1 WHERE token=?";
        return $this->update($query, 
            Array("s", 
                $reqParams["token"]
            )
        );
    }

    public function addUser($reqParams){
        $query = "INSERT INTO users ".
        "(username, user_email, employee_id, password, company)".
        "VALUES (?, ?, ?, ?, ?)";
        return $this->insert($query, 
            Array("sssss", 
                $reqParams["username"], $reqParams["user_email"], $reqParams["employee_id"], 
                $reqParams["password"], $reqParams["company"],
            )
        );
    }

    public function getUsers($reqParams, $limit=0)
    {

        $mapFileType = Array(
            "username"=>Array("s", "upper"),
            "user_email"=>Array("s", "lower"),
            "employee_id"=>Array("s", "upper"),
            "company"=>Array("s", "upper"),
        );
        $queryOptions = getQueryOptions($mapFileType, $reqParams["condition"]);

        // $condition = "";// WHERE ";
        // $params = Array();
        // $format = "";
        // // print_r($reqParams);
        // foreach ($reqParams["condition"] as $elCond) {
        //     $cond = "";

        //     // $value --- 
        //     //    Array($regex: "abc")
        //     foreach ($elCond as $key => $value) {
        //         $transData = Array();
        //         if(is_array($value)) {
        //             try {
        //             $cond .= getSerchOptions($key, $value);
        //             } catch (Error $e) {
        //                 print_r($e);
        //             }
        //         } else {
        //             $transform = strlen($mapFileType[$key][1]) > 0;
        //             if($transform) {
        //                 $transData = getTransformField($mapFileType, $key, $value);
        //             }
        //             $cond .= (($cond ? " AND " : "").($transform ? $transData["field"] : $key)."=? ");
        //             array_push($params, $transform ? $transData["value"] : $key);
        //             $format .= $mapFileType[$key][0];
        //         }
        //     }
        //     if($cond) { 
        //         $condition .= (($condition  ? " OR ":"")."(".$cond.")");
        //     }
        // }
        // $condition = ($condition?" WHERE ": "").$condition;
        // if(strlen($format)) {
        //     // echo $format." -- \n";
        //     array_unshift($params, $format);
        // } else {
        //     // $condition .= "1";
        // }
        // // echo $condition."\n";
        return $this->select("SELECT * FROM users ".$queryOptions["condition"], $queryOptions["params"], $reqParams["sort"]);
    }

    public function updateUser($condParams, $updateData){
        $mapFileType = Array(
            "employee_id"=>Array("s", "upper"),
            "username"=>Array("s", "upper"),
            "user_email"=>Array("s", "lower"),
            "company"=>Array("s", "upper"),
        );

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
        if(isset($updateData["user_email"]) && strlen($updateData["user_email"])) {
            $setFields .= "user_email=?,";
            $format .= "s";
            array_push($setParams, $updateData["user_email"]);
        }
        if(isset($updateData["password"]) && strlen($updateData["password"])) {
            $setFields .= "password=MD5(?),";
            $format .= "s";
            array_push($setParams, $updateData["password"]);
        }
        if(isset($updateData["company"]) && strlen($updateData["company"])) {
            $setFields .= "company=?,";
            $format .= "s";
            array_push($setParams, $updateData["company"]);
        }
        $setFields = rtrim($setFields, ",");
        $queryOptions = getQueryOptions($mapFileType, $condParams);

        $query = "UPDATE users SET ".$setFields.$queryOptions["condition"];

        $format .= $queryOptions["params"][0];
        array_unshift($setParams, $format);
        array_shift($queryOptions["params"]);
        $setParams = array_merge($setParams, $queryOptions["params"]); 

        return $this->update($query, $setParams);
    }

    public function login($query)
    {
        $condition = "WHERE (username='".$query["username"]."' OR user_email='".$query["username"]."') AND password=MD5('".$query["password"]."')";
        return $this->select("SELECT * FROM users ".$condition);
    }

    
    
}
?>