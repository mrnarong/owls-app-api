<?php
require_once PROJECT_ROOT_PATH . "/models/Database.php";
class UserModel extends Database
{
    public function addActivateUser($reqParams){
        // print_r($reqParams);
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
        "status=1 WHERE token=? AND user_email=?";
        $result = $this->update($query, 
            Array("ss", 
                $reqParams["token"],
                $reqParams["email"]
            )
        );
        // print_r($query);
        // print_r($result);

        if($result) {
            $query = "SELECT username, company, user_email as email FROM users WHERE user_email=?";
            $result = $this->select($query, 
            Array("s", 
                $reqParams["email"]
            ));
        
        }

        return $result;
    }

    public function addUser($reqParams){
        // echo "Password: ---> ".$reqParams["password"];
        $query = "INSERT INTO users ".
        "(username, user_email, employee_id, company".(isset($reqParams["password"]) ? ", password" : "").")".
        "VALUES (?, ?, ?, ?".(isset($reqParams["password"]) ? ", MD5(?)" : "").")";
        
        $queryOptions = Array("ssss".(isset($reqParams["password"])? "s" : ""), 
            $reqParams["username"], $reqParams["user_email"], $reqParams["employee_id"], 
            $reqParams["company"],
        );
        if(isset($reqParams["password"])) {
            array_push($queryOptions, $reqParams["password"]);
        }
        $result =  $this->insert($query, $queryOptions);
        $authGroup = "";
        if(isset($reqParams["role_auth_group"])) {
            $authGroup = $reqParams["role_auth_group"];
        }

        $query = "INSERT INTO user_roles ".
        "(username, role".(strlen($authGroup)?", role_auth_group":"").")".
        "VALUES (?, ?".(strlen($authGroup)?", ?":"").")";

        $result =  $this->insert($query, 
            Array("ss".(strlen($authGroup)?"s":""), 
                $reqParams["username"], $reqParams["role"], $reqParams["role_auth_group"]
            )
        );

        // echo "Add user success";

        return $result;
    }

    public function getUsers($reqParams, $limit=0)
    {
        // $t1 = "";
        // $t2 = "";
        // if(isset($reqParams["condition"]["role"])) {
        //     $t1 = "t1.";
        //     $t2 = "t2.";
        // }

        $mapFileType = Array(
            "t1.username"=>Array("s", "upper"),
            "t1.user_email"=>Array("s", "lower"),
            "t1.employee_id"=>Array("s", "upper"),
            "t1.company"=>Array("s", "upper"),
            "t2.role"=>Array("s", "regex"),
        );

        $condition = Array();
        foreach($reqParams["condition"] as $group){
            $newGroup = Array();
            foreach($group as $key=>$value){
                $newGroup[($key=="role"?"t2.":"t1.").$key] = $value; //($key=="role"?"t2.":"t1.")
            }
            array_push($condition, $newGroup);
        }

        /*
SELECT t1.*, t2.role FROM `users` as t1
LEFT JOIN user_roles t2
ON t1.username=t2.username #AND t2.role LIKE '%ADMIN'
WHERE t2.role IS NOT NULL
        */
        $queryOptions = getQueryOptions($mapFileType, $condition); //$reqParams["condition"]);
        $sort = isset($reqParams["sort"]) ? $reqParams["sort"] : "";
        $query = "SELECT t1.*, t2.role FROM `users` as t1
        LEFT JOIN user_roles t2
        ON t1.username=t2.username". #AND t2.role LIKE '%ADMIN'
        ($queryOptions["condition"]? $queryOptions["condition"] : " WHERE t1.username=t2.username"). " AND t2.role IS NOT NULL";
        // echo "\n$query\n";

        // print_r($query);
        return $this->select($query, $queryOptions["params"], $sort);
        // return $this->select("SELECT * FROM users ".$queryOptions["condition"], $queryOptions["params"], $sort);
    }

    
    public function requestResetPassword($username){
        
        return Array("code"=>200, "message"=>"Success");
    }


    public function changePassword($company, $username, $password, $newPassword){
        $query = "SELECT * FROM users WHERE username=?";
        $result = $this->select($query, Array("s", $username), []);
        if(sizeof($result)){
            // echo "Found";
            if(md5($password) != $result[0]["password"] || $company != $result[0]["company"]) {
                // echo "Not match";
                return Array("code"=>442, "message"=>"Username is not found or Wrong Passwod");
            }
            $query = "UPDATE users SET password=MD5(?), reset=0, last_update=NOW() WHERE company=? AND username=? AND password=MD5(?)";
            $this->update($query, Array("ssss", $newPassword, $company, $username, $password));
        } else {
            // echo "Not Found";
            return Array("code"=>442, "message"=>"Username is not found or Wrong Passwod");
        }
        return Array("code"=>200, "message"=>"Success");
    }

    public function getNewUsername($fullname) {
        $idLength = 2;
        $query = "SELECT MAX(username) as username FROM users WHERE username LIKE ?";
        $result = $this->select($query, Array("s", strtolower($fullname)."%"));
        if($result && sizeof($result)) {
            $newUsername = $fullname.str_pad(substr($result[0]["username"], strlen($fullname)) + 1, $idLength, "0", STR_PAD_LEFT);
        } else {
            $newUsername = $fullname.str_pad(1, $idLength, "0", STR_PAD_LEFT);
        }
        return strtolower($newUsername);
    }

    public function updateUser($condParams, $updateData){
        // echo "==================\n";
        $mapFileType = Array(
            "employee_id"=>Array("s", "upper"),
            "username"=>Array("s", "upper"),
            "user_email"=>Array("s", "lower"),
            "company"=>Array("s", "upper"),
        );

        // print_r($condParams);
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

        $query = "UPDATE users SET last_update=NOW(), ".$setFields.$queryOptions["condition"];

        $format .= $queryOptions["params"][0];
        array_unshift($setParams, $format);
        array_shift($queryOptions["params"]);
        $setParams = array_merge($setParams, $queryOptions["params"]); 
        // print_r($condParams); 
        $result = $this->update($query, $setParams);
        // print_r($result);
        // echo "Prepare to update user_roles";
        if($result && isset($updateData["role"])) {
            // echo "\nUpdate Role...\n";
            $setFields = " roles=? WHERE username=?";
            
            $query = "UPDATE user_roles SET  role=? WHERE username=?";

            // echo $query." ".$updateData["role"];
            $username = "";
            foreach($condParams as $cond) {
                foreach($cond as $key=>$value) {
                    // echo " $key ";
                    if($key === "username") {
                        $username = $value;
                        // break;
                    }
                }
            }
            if(strlen($username)){
                $this->update($query, Array("ss", $updateData["role"], isset($username) ? strtoupper($username) : strtoupper($username)));
                // print_r($result);
            }
        }
        return $result;
    }

    public function login($condParams)
    {//REPLACE(`col_name`, ' ', '')
        $query = "SELECT users.*, user_roles.role, REPLACE(roles_auth_group.auth_menu, ' ', '') as auth_menu
        FROM users
        LEFT JOIN user_roles
        ON users.username=user_roles.username
        LEFT JOIN roles_auth_group
        ON user_roles.role_auth_group=roles_auth_group.group_name
        WHERE ";
        $dataParams = Array('ss', $condParams["username"], $condParams["password"]);
        if(strpos($condParams["username"], '@') > -1) {
            $query .= "user_email=?";
        } else {
            $query .= "users.username=?";
        }

        $query .= " AND users.password=MD5(?)";
        // echo $query;
        $result =  $this->select($query, $dataParams);
        // print_r($result);
        return $result;
    }

    public function deleteUser($company, $username)
    {
        // INSERT INTO `users` (`username`, `user_email`, `employee_id`, `password`, `company`, `reset`, `create_datetime`) 
        // VALUES ('DEF', 'nong13@gmail3.com', 'DEF', '81dc9bdb52d04dc20036dbd8313ed055', 'ABC', '0', '2023-12-27 11:16:00')
        if(isset($company) && strlen($company) && isset($username) && strlen($username)) {
            $query = "DELETE FROM users WHERE company=? AND username=?";
            // echo $query;
            $result = $this->update($query, Array("ss", strtoupper($company), strtoupper($username)));
            // print_r($result);
            if($result) {
                $query = "DELETE FROM user_roles WHERE username=?";
                $result = $this->update($query, Array("s", strtoupper($username)));
            }
            return $result;
        }
        return false;
    }

}
?>