<?php
class EmployeeController extends BaseController
{
    public function addEmployee($company, $reqParams){
        $strErrorDesc = '';
        $arrQueryStringParams = $this->getQueryStringParams();
        try {
            $employeeModel = new EmployeeModel();
            $responseData = $employeeModel->addEmployee(
                    $company,
                // array_map(function($item) {
                    Array(
                        "employee_id" => $reqParams["employeeId"],
                        "username" => $reqParams["username"],
                        "email" => $reqParams["email"],
                        "fullname" => $reqParams["fullname"],
                        "fullname_en" => $reqParams["fullnameEn"],
                        "id_card_no" => $reqParams["idCardNo"],
                        "gender" => $reqParams["gender"],
                        "birthdate" => $reqParams["birthDate"],
                        "address" => $reqParams["address"],
                        "enroll_date" => $reqParams["enrollDate"],
                        "contact_no" => $reqParams["contactNo"],
                        "contact_person" => $reqParams["contactPerson"],
                        "role" => $reqParams["role"],
                        "department" => $reqParams["department"],
                        )
            );
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return true;
    }

    public function getEmployee($company, $reqParams, $limit=0){
        $strErrorDesc = '';
        try {
            $employeeModel = new EmployeeModel();
            $responseData = $employeeModel->getEmployees($company, $reqParams, $limit);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
            // employee_id, username, email, fullname, gender, birthdate, enroll_date, contact_no, contact_person, role, department
            return array_map(function($item) {
                return Array(
                    "employeeId" => $item["employee_id"],
                    "username" => $item["username"],
                    "email" => $item["email"],
                    "fullname" => $item["fullname"],
                    "fullnameEn" => $item["fullname_en"],
                    "idCardNo" => $item["id_card_no"],
                    "gender" => $item["gender"],
                    "birthDate" => $item["birthdate"],
                    "enrollDate" => $item["enroll_date"],
                    "contactNo" => $item["contact_no"],
                    "contactPerson" => $item["contact_person"],
                    "role" => $item["role"],
                    "department" => $item["department"],
                );
            
            }, $responseData);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function updateEmployee($condition, $updateData){
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
                //     $mappingCondition["email"] = $value;
                // }
            }
            array_push($mappingCondition, $itemCond);
        }

        $mappingUpdate = Array();
        try {
            if(isset($updateData["employeeId"])){
                $mappingUpdate["employee_id"] = $updateData["employeeId"];
            }
            if(isset($updateData["username"])){
                $mappingUpdate["username"] = $updateData["username"];
            }
            if(isset($updateData["email"])){
                $mappingUpdate["email"] = $updateData["email"];
            }
            if(isset($updateData["fullname"])){
                $mappingUpdate["fullname"] = $updateData["fullname"];
            }
            if(isset($updateData["gender"])){
                $mappingUpdate["gender"] = $updateData["gender"];
            }
            if(isset($updateData["birthdate"])){
                $mappingUpdate["birthdate"] = $updateData["birthdate"];
            }
            if(isset($updateData["enrollDate"])){
                $mappingUpdate["enroll_date"] = $updateData["enrollDate"];
            }
            if(isset($updateData["contactNo"])){
                $mappingUpdate["contact_no"] = $updateData["contactNo"];
            }
            if(isset($updateData["contactPerson"])){
                $mappingUpdate["contact_person"] = $updateData["contactPerson"];
            }
            if(isset($updateData["role"])){
                $mappingUpdate["role"] = $updateData["role"];
            }
            if(isset($updateData["department"])){
                $mappingUpdate["department"] = $updateData["department"];
            }
            // if(isset($updateData["birthdate"])){
            //     $mappingUpdate["birthdate"] = $updateData["birthdate"];
            // }
            
            $employeeModel = new EmployeeModel();
            $responseData = $employeeModel->updateEmployee($mappingCondition, $mappingUpdate);
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }

        return $responseData;
    }
}
