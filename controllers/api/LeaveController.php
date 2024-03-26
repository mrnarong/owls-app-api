<?php
require_once("../../inc/utils.php");
class LeaveController extends BaseController
{
    public function addLeave($company, $reqParams)
    {
        $strErrorDesc = '';
        // $arrQueryStringParams = $this->getQueryStringParams();
        try {

            $leaveData = array(
                "employee_id" => $reqParams["leaveData"]["employeeId"],
                "type" => $reqParams["leaveData"]["type"],
                "leave_reason" => $reqParams["leaveData"]["leaveReason"],
                "start_date" => $reqParams["leaveData"]["startDate"],
                "end_date" => $reqParams["leaveData"]["endDate"],
                "days" => $reqParams["leaveData"]["totalDays"],
                "issue_date" => $reqParams["leaveData"]["issueDate"],
                // "documents" => $reqParams["leaveData"]["documents"],
            );

            if ($reqParams["leaveData"]["documents"]) {
                $util = new Utils();
                $documents = "";

                // echo "Saving document file...", $reqParams["leaveData"]["documents"][0];

                $files = $reqParams["leaveData"]["documents"];
                $docDate = preg_replace('/-|\s|:/', "", $reqParams["leaveData"]["startDate"]);
                $prefix = "{$company}_{$reqParams["leaveData"]["employeeId"]}_leavedoc_";

                for ($i = 0; $i < count($files); $i++) {
                    $fileName = sprintf('%s%s_%03d.%s', $prefix, $docDate, $i + 1, $util->getImageBase64FileExt($files[$i]));
                    // echo "\n=========\n$fileName\n=========\n";
                    
                    $documents .= ($fileName . ",");
                    // /Users/narong/www/owlapp/api/controllers/api/EmployeeController.php
                    $util->saveFile($files[$i], "../../../app_data/leaves/" . $fileName);
                }
                if (strlen($documents)) {
                    $documents = rtrim($documents, ",");
                    $leaveData["documents"] = $documents;
                }
            }

            $leaveModel = new LeaveModel();
            $responseData = $leaveModel->addLeave($company, $leaveData);
            return $responseData;
        } catch (Error $e) {
            print_r($e);
            $strErrorDesc = $e->getMessage();
            return array("error" => $strErrorDesc);
        }
        // return true;
    }

    public function getLeaves($company, $reqParams, $limit = 0)
    {
        $strErrorDesc = '';
        try {
            $leaveModel = new LeaveModel();
            $responseData = $leaveModel->getLeaves($company, $reqParams, $limit);
            // print_r($responseData);

        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
            return array_map(function ($item) {
                return array(
                    "recId" => $item["rec_id"],
                    "company" => $item["company"],
                    "employeeId" => $item["employee_id"],
                    "fullname" => $item["fullname"],
                    "type" => $item["type"],
                    "leaveReason" => $item["leave_reason"],
                    "issueDate" => $item["create_datetime"],
                    "startDate" => $item["start_date"],
                    "endDate" => $item["end_date"],
                    "totalDays" => $item["days"],
                    "approveDate" => $item["employee_leaves.approve_date"],
                    "status" => $item["status"],
                    "approveNote" => $item["approval_note"],
                    "remark" => $item["remark"],
                );
            }, $responseData);
        } else {
            return array("error" => $strErrorDesc);
        }
    }

    public function updateLeave($company, $condition, $updateData)
    {

        try {
            $leaveModel = new LeaveModel();
            $responseData = $leaveModel->updateLeave($company, $condition, $updateData);
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return array("error" => $strErrorDesc);
        }
        return $responseData;
    }

    // public function approveLeave($reqParams){
    //     $strErrorDesc = '';
    //     try {
    //         $leaveModel = new LeaveModel();
    //         $updateRequest = Array(
    //             "rec_id" => $reqParams["recId"],
    //             "approve_status" => $reqParams["approveStatus"],
    //             "approve_date" => $reqParams["approveDate"],
    //             "approve_by" => $reqParams["approvedBy"],
    //             // "approve_reason" => $reqParams["approveReason"],
    //         );
    //         if($reqParams["approveStatus"]) {
    //             $reqParams["approve_reason"] = $reqParams["approveReason"];
    //         }
    //         $responseData = $leaveModel->updateLeave($updateRequest);
    //     } catch (Error $e) {
    //         $strErrorDesc = $e->getMessage();
    //         return Array("error"=>$strErrorDesc);
    //     }
    //     return $responseData;
    // }

    public function getLeaveSummary($company, $reqParams, $limit = 0)
    {
        $strErrorDesc = '';
        try {
            $leaveModel = new LeaveModel();
            $responseData = $leaveModel->getLeaveSummary($company, $reqParams, $limit);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
            return array_map(function ($item) {
                return array(
                    "company" => $item["company"],
                    "employeeId" => $item["employee_id"],
                    "monthYear" => $item["month_year"],
                    "type" => $item["type"],
                    "status" => $item["status"],
                    "totalDays" => $item["total_days"],
                );
            }, $responseData);
        } else {
            return array("error" => $strErrorDesc);
        }
    }
}
