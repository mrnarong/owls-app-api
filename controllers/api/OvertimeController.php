<?php
class OvertimeController extends BaseController
{

    public function addOvertime($company, $reqParams){
        $strErrorDesc = '';
        // $arrQueryStringParams = $this->getQueryStringParams();
        // echo "addExpense\n";
        try {
            $model = new OvertimeModel();
            $responseData = $model->addOvertime($company, 
                    Array(
                        "employee_id" => $reqParams["overtimeData"]["employeeId"],
                        // "issue_date" => $reqParams["overtimeData"]["itemDate"], // Auto time stamp
                        "project" => $reqParams["overtimeData"]["project"],
                        "detail" => $reqParams["overtimeData"]["detail"],
                        "start_datetime" => $reqParams["overtimeData"]["startDate"],
                        "end_datetime" => $reqParams["overtimeData"]["endDate"],
                    )
            );
        } catch (Error $e) {
            print_r($e);
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return true;
    }

    public function getOvertimes($company, $reqParams, $limit=0){
        $strErrorDesc = '';
        // echo "getExpenses controller";
        try {
            $model = new OvertimeModel();
            $responseData = $model->getOvertimes($company, $reqParams, $limit);
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
                    "startDate" => $item["start_datetime"],
                    "endDate" => $item["end_datetime"],
                    "project" => $item["project"],
                    "detail" => $item["detail"],
                    "status" => $item["status"],
                    "approvedBy" => $item["approved_by"],
                    "approvalNote" => $item["approval_note"],
                    "approveDate" => $item["approve_date"]
                );
            
            }, $responseData);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function updateOvertime($condition, $updateData){
        $strErrorDesc = '';

        try{
            
            $model = new OvertimeModel();
            $responseData = $model->updateOvertime($condition, $updateData);
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }

        return $responseData;
    }
}
