<?php
require_once("../../inc/utils.php");
class UserController extends BaseController
{

    public function addActivateUser($reqParams){
        $strErrorDesc = '';
        // $arrQueryStringParams = $this->getQueryStringParams();
        // print_r($reqParams);

        try {
            $userModel = new UserModel();
            $responseData = $userModel->addActivateUser(
                // array_map(function($item) {
                Array(
                    "token" => $reqParams["token"],
                    "user_email" => $reqParams["email"],
                    // "create_time" => $reqParams["createTime"],
                    // "valide_time" => $reqParams["valideTime"],
                    // "status" => $reqParams["status"],
                    )
            );
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return true;
    }

    public function setActivateStatus($reqParams){
        $strErrorDesc = '';
        // $arrQueryStringParams = $this->getQueryStringParams();
        try {
            $userModel = new UserModel();
            $responseData = $userModel->setActivateStatus(
                $reqParams
            );
            // print_r($responseData);
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return $responseData;
    }


    public function login($query) {
        $strErrorDesc = false;
        try {
            $userModel = new UserModel();
            $responseData = $userModel->login($query);

        } catch (Error $e) {
            $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
        }
        if (!$strErrorDesc) {
            return $responseData;
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function addUser($reqParams){
        $strErrorDesc = '';
        // $arrQueryStringParams = $this->getQueryStringParams();
        // print_r($reqParams);
        try {
            $userModel = new UserModel();
            $newData = Array(
                "username" => $reqParams["username"],
                "user_email" => $reqParams["email"],
                "employee_id" => $reqParams["employeeId"],
                "password" => $reqParams["password"],
                "company" => $reqParams["company"],
                "role" => $reqParams["role"],
                
                );
            if(isset($reqParams["roleAuthGroup"])) {
                $newData["role_auth_group"] = $reqParams["roleAuthGroup"];
            }
            $responseData = $userModel->addUser($newData);
            return $responseData;
        } catch (Error $e) {
            print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        // return true;
    }

    public function getUser($reqParams, $limit=0){
        $strErrorDesc = '';
        try {
            $userModel = new UserModel();
            $responseData = $userModel->getUsers($reqParams, $limit);

        } catch (Error $e) {
            $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
        }
        
        // send output
        if (!$strErrorDesc) {
            return array_map(function($item) {
                return Array(
                    "company" => $item["company"],
                    "employeeId" => $item["employee_id"],
                    "username" => $item["username"],
                    "email" => $item["user_email"],
                    "role" => $item["role"],
                    // "type" => $item["wage_type"],
                    // "amount" => $item["amount"],
                    // "status" => $item["status"],
                );
            
            }, $responseData);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    // public function requestResetPassword($username){
    //     $strErrorDesc = '';
    //     $responseData = false;
    //     try {
    //         $userModel = new UserModel();
    //         $users = $userModel->getUsers(Array("condition"=>Array(Array("username"=>$username))));
    //         if(sizeof($users)) {


    //             $util = new Utils();
    //             $util->sendMail(
    //                 $users[0]["user_email"], 
    //                 "mrnarong@gmail.com", 
    //                 "แจ้งเตือนการเข้าใช้งานระบบ",
    //                 "ขณะนี้ทางบริษัทได้ทำการเพิ่มข้อมูลของท่านเข้าระบบแล้ว กรุณาเข้า <a href='http://hr-app.owlswallpapers.com/owl-client/login'>หน้า login</a> ด้วยข้อมูลต่อไปนี้ <br><br> Username: <br> Password: <br><br>เพื่อทำการกำหนดรหัสผ่านสำหรับเข้าใช้งานครั้งต่อไป<br>",
    //                 "ขณะนี้ทางบริษัทได้ทำการเพิ่มข้อมูลของท่านเข้าระบบแล้ว กรุณาเข้า http://hr-app.owlswallpapers.com/owl-client/login ด้วยข้อมูลต่อไปนี้\n Username: \n Password: \n เพื่อทำการกำหนดรหัสผ่านสำหรับเข้าใช้งานครั้งต่อไป\n"
    //             );
    //             // $responseData = $userModel->requestResetPassword($username);
    //         }
    //         print_r($users);
    //         $responseData = [];

    //     } catch (Error $e) {
    //         $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
    //     }
        
    //     // send output
    //     if (!$strErrorDesc) {
    //         return $responseData;
    //     } else {
    //         return Array("error"=>$strErrorDesc);
    //     }
    // }

    public function changePassword($company, $username, $password, $newPassword){
        $strErrorDesc = '';
        try {
            $userModel = new UserModel();
            $responseData = $userModel->changePassword($company, $username, $password, $newPassword);

        } catch (Error $e) {
            $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
        }
        
        // send output
        if (!$strErrorDesc) {
            return $responseData;
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }
    public function getNewUsername($fullname) {

        $strErrorDesc = '';
        try {
            $userModel = new UserModel();
            return $userModel->getNewUsername($fullname);
        } catch (Error $e) {
            // $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
            return strtolower($fullname)."01";
        }

    }

    public function updateUser($condition, $updateData){
        $strErrorDesc = '';
        $mappingCondition = Array();
        foreach($condition as $elCond){ 
            $itemCond = Array();
            foreach($elCond as $key=>$value) {
                if($key == "company") {
                    $itemCond["company"] = $value;
                }
                if($key == "employeeId") {
                    $itemCond["employee_id"] = $value;
                }
                if($key == "username") {
                    $itemCond["username"] = $value;
                }
                // if($key == "email") {
                //     $itemCond["user_email"] = $value;
                // }
            }
            array_push($mappingCondition, $itemCond);
        }
        // reset($condition);
        // print_r($condition);
        // print_r($mappingCondition);

        $mappingUpdate = Array();
        try {
            $mappingUpdate = Array();
            if(isset($updateData["username"]) && strlen($updateData["username"])){
                $mappingUpdate["username"] = $updateData["username"];
            }
            if(isset($updateData["email"]) && strlen($updateData["email"])){
                $mappingUpdate["user_email"] = $updateData["email"];
            }
            if(isset($updateData["employeeId"]) && strlen($updateData["employeeId"])){
                $mappingUpdate["employee_id"] = $updateData["employeeId"];
            }
            if(isset($updateData["password"]) && strlen($updateData["password"])){
                $mappingUpdate["password"] = $updateData["password"];
            }
            if(isset($updateData["company"]) && strlen($updateData["company"])){
                $mappingUpdate["company"] = $updateData["company"];
            }
            if(isset($updateData["role"]) && strlen($updateData["role"])){
                $mappingUpdate["role"] = $updateData["role"];
            }
            
            $userModel = new UserModel();
            $responseData = $userModel->updateUser($mappingCondition, $mappingUpdate);
                // array_map(function($item) {
            //     Array(
            //         "username" => $reqParams["username"],
            //         "user_email" => $reqParams["email"],
            //         "employee_id" => $reqParams["employeeId"],
            //         "password" => $reqParams["password"],
            //         "company" => $reqParams["company"],
            //         )
            // );
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return $responseData;
    }

    public function deleteUser($company, $username){
        $strErrorDesc = '';
        try {
            $userModel = new UserModel();
            $responseData = $userModel->deleteUser($company, $username);

        } catch (Error $e) {
            $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
        }
        
        // send output
        if (!$strErrorDesc) {
            return $responseData;
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

}
