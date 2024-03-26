<?php
require_once("../../inc/utils.php");

class EmployeeController extends BaseController
{
    public function addEmployee($company, $reqParams){
        $strErrorDesc = '';


        $documents = "";
        $files = $reqParams["documents"];
        $docDate = preg_replace('/-|\s|:/', "", $reqParams["enrollDate"]);
        $prefix = "{$company}_{$reqParams["employeeId"]}_appdoc_";

        $util = new Utils();
        for($i = 0;$i < count($files);$i++) {
            $fileName = sprintf('%s%s_%03d.%s', $prefix, $docDate, $i+1, $util->getImageBase64FileExt($files[$i]));
            $documents .= ($fileName.",");
            // /Users/narong/www/owlapp/api/controllers/api/EmployeeController.php
            $util->saveFile($files[$i], "../../../app_data/employee/".$fileName);
        }
        if(strlen($documents)) {
            $documents = rtrim($documents, ",");
        }

        // echo "Documents: ". $documents;

        // echo "addEmployee";


        try {
            $employeeModel = new EmployeeModel();
            $responseData = $employeeModel->addEmployee(
                    $company,
                // array_map(function($item) {
                    Array(
                        "employee_id" => $reqParams["employeeId"],
                        "fullname" => $reqParams["fullname"],
                        "fullname_en" => $reqParams["fullnameEn"],
                        "mobile_no" => $reqParams["mobileNo"],
                        "birthdate" => $reqParams["birthDate"],
                        "id_number" => $reqParams["idNumber"],
                        "gender" => $reqParams["gender"],

                        "enroll_date" => $reqParams["enrollDate"],
                        // "position" => $reqParams["position"],
                        // "department" => $reqParams["department"],
                        // "salary" => $reqParams["salary"],
                        "employ_type" => $reqParams["employType"],
                        "payment_type" => $reqParams["paymentType"],
                        "bank" => $reqParams["bank"],
                        "bank_acc_no" => $reqParams["bankAccNo"],

                        "username" => $reqParams["username"],
                        "email" => $reqParams["email"],
                        "role" => $reqParams["role"],

                        "contact_no" => $reqParams["contactNo"],
                        "contact_person" => $reqParams["contactPerson"],
                        "relation" => $reqParams["relation"],

                        "documents" => $documents,
                        "address" => $reqParams["address"],
                        )
            );
            // print_r($responseData);
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
        // print_r($responseData);
        if (!$strErrorDesc) {
            // employee_id, username, email, fullname, gender, birthdate, enroll_date, contact_no, contact_person, role, department
            return array_map(function($item) {
                return Array(
                    "recId" => $item["rec_id"],
                    "company" => $item["company"],
                    "employeeId" => $item["employee_id"],
                    "fullname" => $item["fullname"],
                    "fullnameEn" => $item["fullname_en"],
                    "idNumber" => $item["id_number"],
                    "mobileNo" => $item["mobile_no"],
                    "gender" => $item["gender"],
                    "birthDate" => $item["birthdate"],

                    "enrollDate" => $item["enroll_date"],
                    "documents" => $item["documents"],
                    "contactNo" => $item["contact_no"],
                    "contactPerson" => $item["contact_person"],

                    "salaryRefId" => $item["salary_ref_id"],
                    "department" => $item["department"],
                    "position" => $item["position"],
                    "salary" => $item["salary"],
                    "salaryEffectiveDate" => $item["salary_effective_date"],
                    
                    "employType" => $item["employ_type"],

                    "paymentType" => $item["payment_type"],
                    "bank" => $item["bank"],
                    "bankAccNo" => $item["bank_acc_no"],

                    "emerContact" => $item["contact_person"],
                    "emerMobileNo" => $item["contact_no"],
                    "emerRelation" => $item["relation"],

                    "username" => $item["uname"],
                    "userRole" => $item["role"],
                    "email" => $item["email"],
                    "hasLoggedIn" => !$item["reset"],

                );
            
            }, $responseData);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function lookupEmployee($company, $reqParams, $limit=0){
        $strErrorDesc = '';
        try {
            $employeeModel = new EmployeeModel();
            $responseData = $employeeModel->getEmployees("", $reqParams, $limit);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }
        // print_r($responseData);
        if (!$strErrorDesc) {
            // $result = Array(
            //     "recId" => $item["rec_id"],
            //     "company" => $item["company"],
            //     "employeeId" => $item["employee_id"],
            // );
            // print_r($reqParams);

            return array_map(function($item) {
                $result = Array(
                    "recId" => $item["rec_id"],
                    "company" => $item["company"],
                    "employeeId" => $item["employee_id"],
                );
                return $result;
            
            }, $responseData);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function getNewEmployeeId($company) {
        try {
            $model = new EmployeeModel();
            $result = $model->getNewEmployeeId($company);
            return $result;
        } catch (Error $e) {
            return $company.str_pad(1, 4, "0", STR_PAD_LEFT);;
        }
    }

    public function updateEmployee($company, $condition, $updateData){
        // $strErrorDesc = '';

        // $mappingCondition = Array();
        // foreach($condition as $elCond){ 
        //     $itemCond = Array();
        //     foreach($elCond as $key=>$value) {
        //         if($key == "employeeId") {
        //             $itemCond["employee_id"] = $value;
        //         }
        //         if($key == "username") {
        //             $itemCond["username"] = $value;
        //         }
        //         // if($key == "email") {
        //         //     $mappingCondition["email"] = $value;
        //         // }
        //     }
        //     array_push($mappingCondition, $itemCond);
        // }

        // $mappingUpdate = Array();
        try {
            // if(isset($updateData["employeeId"])){
            //     $mappingUpdate["employee_id"] = $updateData["employeeId"];
            // }
            // if(isset($updateData["username"])){
            //     $mappingUpdate["username"] = $updateData["username"];
            // }
            // if(isset($updateData["email"])){
            //     $mappingUpdate["email"] = $updateData["email"];
            // }
            // if(isset($updateData["fullname"])){
            //     $mappingUpdate["fullname"] = $updateData["fullname"];
            // }
            // if(isset($updateData["gender"])){
            //     $mappingUpdate["gender"] = $updateData["gender"];
            // }
            // if(isset($updateData["birthdate"])){
            //     $mappingUpdate["birthdate"] = $updateData["birthdate"];
            // }
            // if(isset($updateData["enrollDate"])){
            //     $mappingUpdate["enroll_date"] = $updateData["enrollDate"];
            // }
            // if(isset($updateData["contactNo"])){
            //     $mappingUpdate["contact_no"] = $updateData["contactNo"];
            // }
            // if(isset($updateData["contactPerson"])){
            //     $mappingUpdate["contact_person"] = $updateData["contactPerson"];
            // }
            // if(isset($updateData["role"])){
            //     $mappingUpdate["role"] = $updateData["role"];
            // }
            // if(isset($updateData["department"])){
            //     $mappingUpdate["department"] = $updateData["department"];
            // }
            // if(isset($updateData["birthdate"])){
            //     $mappingUpdate["birthdate"] = $updateData["birthdate"];
            // }
            
            $employeeModel = new EmployeeModel();
            // $responseData = $employeeModel->updateEmployee($mappingCondition, $mappingUpdate);
            $responseData = $employeeModel->updateEmployee($company, $condition, $updateData);
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }

        return $responseData;
    }

    // Called by update employee
    public function updateSalaryHist($company, $employeeId, $position, $department, $salary){

        try {
            $employeeModel = new EmployeeModel();
            $responseData = $employeeModel->updateSalaryHist($company, $employeeId, $position, $department, $salary);
            // print_r($responseData);
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }

        return true; //$responseData;
    }

    public function addSalary($company, $employeeId, $salary, $department, $position, $effectiveDate, $note, $status="WAITING"){
        $strErrorDesc = '';
        // $arrQueryStringParams = $this->getQueryStringParams();
        try {
            $model = new EmployeeModel();
            $model->addSalary($company, $employeeId, $salary, $department, $position, $effectiveDate, $note, $status);
        } catch (Error $e) {
            // print_r($e);
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return true;
    }

    public function getCurrentSalary($company, $employeeId, $status) {

    }
    
    public function getSalaryHist($company, $condition){
        $strErrorDesc = '';
        // $arrQueryStringParams = $this->getQueryStringParams();
        try {
            $model = new EmployeeModel();
            $responseData = $model->getSalaryHist($company, $condition);

            return array_map(function($item) {
                return Array(
                    "recId" => $item["rec_id"],
                    "company" => $item["company"],
                    "employeeId" => $item["employee_id"],
                    "fullname" => $item["fullname"],
                    "department" => $item["department"],
                    "position" => $item["position"],
                    "salary" => $item["salary"],
                    "effectiveDate" => $item["effective_date"],
                    "status" => $item["status"],
                );
            
            }, $responseData);
        } catch (Error $e) {
            // print_r($e);
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        // return true;
    }


    public function updateSalary($recId, $updateData){
        $strErrorDesc = '';
        // $arrQueryStringParams = $this->getQueryStringParams();
        try {
            $model = new EmployeeModel();
            return $model->updateSalary($recId, $updateData);
        } catch (Error $e) {
            // print_r($e);
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        // return true;
    }

    
}
