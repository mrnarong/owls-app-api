<?php
class UserController extends BaseController
{

    public function addActivateUser($reqParams){
        $strErrorDesc = '';
        $arrQueryStringParams = $this->getQueryStringParams();
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
        $arrQueryStringParams = $this->getQueryStringParams();
        try {
            $userModel = new UserModel();
            $responseData = $userModel->setActivateStatus(
                // array_map(function($item) {
                Array(
                    "token" => $reqParams["token"]
                )
            );
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return $responseData;
    }


    public function login($query) {
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
        $arrQueryStringParams = $this->getQueryStringParams();
        try {
            $userModel = new UserModel();
            $responseData = $userModel->addUser(
                // array_map(function($item) {
                Array(
                    "username" => $reqParams["username"],
                    "user_email" => $reqParams["email"],
                    "employee_id" => $reqParams["employeeId"],
                    "password" => $reqParams["password"],
                    "company" => $reqParams["company"],
                    )
            );
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return true;
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
            return $responseData;
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function updateUser($condition, $updateData){
        $strErrorDesc = '';
        $mappingCondition = Array();
        foreach($condition as $elCond){ 
            $itemCond = Array();
            foreach($elCond as $key=>$value) {
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

        $mappingUpdate = Array();
        try {
            $mappingUpdate = Array();
            if(isset($updateData["username"])){
                $mappingUpdate["username"] = $updateData["username"];
            }
            if(isset($updateData["email"])){
                $mappingUpdate["user_email"] = $updateData["email"];
            }
            if(isset($updateData["employeeId"])){
                $mappingUpdate["employee_id"] = $updateData["employeeId"];
            }
            if(isset($updateData["password"])){
                $mappingUpdate["password"] = $updateData["password"];
            }
            if(isset($updateData["company"])){
                $mappingUpdate["company"] = $updateData["company"];
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

}
