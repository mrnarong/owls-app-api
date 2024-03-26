<?php
class CommissionController extends BaseController
{
    

    public function addCommission($company, $reqParams){
        $strErrorDesc = '';
        try {
            $model = new CommissionModel();
            $responseData = $model->addCommission($company, $reqParams);
        } catch (Error $e) {
            print_r($e);
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return true;
    }

    public function getCommissions($company, $reqParams, $limit=0){
        $strErrorDesc = '';
        // echo "getCommissions controller";
        try {
            $model = new CommissionModel();
            $responseData = $model->getCommissions($company, $reqParams, $limit);
        } catch (Error $e) {
            echo $e;
            $strErrorDesc = $e->getMessage();
        }
        // print_r($responseData);
        if (!$strErrorDesc) {
            // employee_id, username, email, fullname, gender, birthdate, enroll_date, contact_no, contact_person, role, department
            return array_map(function($item) {
                return Array(
                    "recId" => $item["rec_id"],
                    "employeeId" => $item["employee_id"],
                    "company" => $item["company"],
                    "fullname" => $item["fullname"],
                    "issueDate" => $item["issue_date"],
                    "role" => $item["role"],
                    "amount" => $item["amount"],
                    "type" => $item["type"],
                    "project" => $item["project"],
                    "projectValue" => $item["project_value"],
                    "approvedBy" => $item["approved_by"],
                    "approveDate" => $item["approve_date"],
                    "approvalNote" => $item["approval_note"],
                    "status" => $item["status"],
                );
            
            }, $responseData);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function updateCommission($condition, $updateData){
        $strErrorDesc = '';

        try{
            
            $model = new CommissionModel();
            $responseData = $model->updateCommission($condition, $updateData);
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }

        return $responseData;

    }

}
