<?php
class LeaveController extends BaseController
{
    public function addLeave($reqParams){
        $strErrorDesc = '';
        $arrQueryStringParams = $this->getQueryStringParams();
        try {
            $leaveModel = new LeaveModel();
            $responseData = $leaveModel->addLeave(
                    Array(
                        "employee_id" => $reqParams["employeeId"],
                        "type" => $reqParams["type"],
                        "leave_reason" => $reqParams["leaveReason"],
                        "start_date" => $reqParams["startDate"],
                        "end_date" => $reqParams["endDate"],
                        "issue_date" => $reqParams["issueDate"],
                    )
            );
        } catch (Error $e) {
            print_r($e);
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return true;
    }

    public function getLeaves($reqParams, $limit=0){
        $strErrorDesc = '';
        try {
            $leaveModel = new LeaveModel();
            $responseData = $leaveModel->getLeaves($reqParams, $limit);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
            return array_map(function($item) {
                return Array(
                    "recId" => $item["rec_id"],
                    "employeeId" => $item["employee_id"],
                    "fullname" => $item["fullname"],
                    "type" => $item["type"],
                    "leaveReson" => $item["leave_reason"],
                    "issueDate" => $item["issue_date"],
                    "startDate" => $item["start_date"],
                    "endDate" => $item["end_date"],
                    "approveDate" => $item["approve_date"],
                    "approveStatus" => $item["approve_status"],
                    "approveReason" => $item["approve_reason"],
                    "remark" => $item["remark"],
                );
            
            }, $responseData);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function updateLeave($condition, $updateData){
        // $strErrorDesc = '';
        // try {
        //     $leaveModel = new LeaveModel();
        //     $responseData = $leaveModel->updateLeave(
        //             Array(
        //                 "rec_id" => $reqParams["recId"],
        //                 "employee_id" => $reqParams["employeeId"],
        //                 )
        //     );
        // } catch (Error $e) {
        //     $strErrorDesc = $e->getMessage();
        //     return Array("error"=>$strErrorDesc);
        // }
        // return $responseData;
        $emptyCond = true;
        $mappingCondition = Array();
        foreach($condition as $elCond){ 
            $itemCond = Array();
            foreach($elCond as $key=>$value) {
                if($key == "recId") {
                    $itemCond["rec_id"] = $value;
                    $emptyCond = false;
                }
            }
            array_push($mappingCondition, $itemCond);
        }

        // if(emptyCond) {
        //     return Array("error"=>$strErrorDesc);
        // }

        $mappingUpdate = Array();
        try {
            $mappingUpdate = Array();
            if(isset($updateData["employeeId"])){
                $mappingUpdate["employee_id"] = $updateData["employeeId"];
            }
            if(isset($updateData["type"])){
                $mappingUpdate["type"] = $updateData["type"];
            }
            if(isset($updateData["leaveReason"])){
                $mappingUpdate["leave_reason"] = $updateData["leaveReason"];
            }
            if(isset($updateData["issueDate"])){
                $mappingUpdate["issue_date"] = $updateData["issueDate"];
            }
            if(isset($updateData["startDate"])){
                $mappingUpdate["start_date"] = $updateData["startDate"];
            }
            if(isset($updateData["endDate"])){
                $mappingUpdate["end_date"] = $updateData["endDate"];
            }
            if(isset($updateData["approveDate"])){
                $mappingUpdate["approve_date"] = $updateData["approveDate"];
            }
            if(isset($updateData["approveStatus"])){
                $mappingUpdate["approve_status"] = $updateData["approveStatus"];
            }
            if(isset($updateData["approveReason"])){
                $mappingUpdate["approve_reason"] = $updateData["approveReason"];
            }            
            if(isset($updateData["remark"])){
                $mappingUpdate["remark"] = $updateData["remark"];
            }
            
            $leaveModel = new LeaveModel();
            $responseData = $leaveModel->updateLeave($mappingCondition, $mappingUpdate);
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return $responseData;
    }

    public function approveLeave($reqParams){
        $strErrorDesc = '';
        try {
            $leaveModel = new LeaveModel();
            $updateRequest = Array(
                "rec_id" => $reqParams["recId"],
                "approve_status" => $reqParams["approveStatus"],
                "approve_date" => $reqParams["approveDate"],
                "approve_by" => $reqParams["approvedBy"],
                // "approve_reason" => $reqParams["approveReason"],
            );
            if($reqParams["approveStatus"]) {
                $reqParams["approve_reason"] = $reqParams["approveReason"];
            }
            $responseData = $leaveModel->updateLeave($updateRequest);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return $responseData;
    }
}
