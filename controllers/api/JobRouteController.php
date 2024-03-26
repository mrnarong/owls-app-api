<?php
class JobRouteController extends BaseController
{

    function getImageBase64FileExt($base64) {
        // $base64 = 'data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAPYA…g4ODg4ODg4ODg4DB//P+x99hmgz+VBwAAAABJRU5ErkJggg==';
        $ftags = explode(';', $base64);
        $posType = strpos($ftags[0], "/");
        return substr($ftags[0], $posType+1);
    }

    function saveFile($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' ); 
    
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );
    
        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );
    
        // clean up the file resource
        fclose( $ifp ); 
    
        return $output_file; 
    }

    public function addJobRoute($company, $reqParams){
        $strErrorDesc = '';
        // $arrQueryStringParams = $this->getQueryStringParams();

        $documents = "";
        
        $files = Array($reqParams["jobRoute"]["photo"]);

        // echo sizeof($files);

        try {

            $docDate = preg_replace('/-|\s|:/', "", $reqParams["jobRoute"]["routingDate"]); //str_replace("-", $reqParams["expenseData"]["itemDate"])
            $prefix = "{$company}_{$reqParams["jobRoute"]["employeeId"]}_jobroute_";
            for($i = 0;$i < count($files);$i++) {
                $fileName = sprintf('%s%s_%03d.%s', $prefix, $docDate, $i+1, $this->getImageBase64FileExt($files[$i]));
                $documents .= ($fileName.",");
                // echo "../app_data/".$fileName."\n";
                $this->saveFile($files[$i], "../../../app_data/jobroutes/".$fileName);
            }
            $documents = rtrim($documents, ",");
            
            $model = new JobRouteModel();
            $responseData = $model->addJobRoute($company,
                Array(
                    "employee_id" => $reqParams["jobRoute"]["employeeId"],
                    "routing_date" => $reqParams["jobRoute"]["routingDate"],
                    "project" => $reqParams["jobRoute"]["project"],

                    "origin_place" => $reqParams["jobRoute"]["originPlace"],
                    "origin_lat" => $reqParams["jobRoute"]["originLat"],
                    "origin_lng" => $reqParams["jobRoute"]["originLng"],

                    "dest_place" => $reqParams["jobRoute"]["destPlace"],
                    "dest_lat" => $reqParams["jobRoute"]["destLat"],
                    "dest_lng" => $reqParams["jobRoute"]["destLng"],

                    "distance" => $reqParams["jobRoute"]["distance"],
                    "documents" => $documents,
                )
            );

            
        } catch (Error $e) {
            // print_r($e);
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return true;
    }

    public function getJobRoutes($company, $reqParams, $limit=0){
        // print_r($reqParams);
        $strErrorDesc = '';
        try {
            $model = new JobRouteModel();
            $responseData = $model->getJobRoutes($company, $reqParams, $limit);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
        }

        if (!$strErrorDesc) {
            $responseData = array_map(function($item) {
                return Array(
                    "recId" => $item["rec_id"],
                    "company" => $item["company"],
                    "employeeId" => $item["employee_id"],
                    "fullname" => $item["fullname"],
                    "routingDate" => $item["routing_date"],
                    "project" => $item["project"],
                    "originPlace" => $item["origin_place"],
                    "originLat" => $item["origin_lat"],
                    "originLng" => $item["origin_lng"],
                    "destPlace" => $item["dest_place"],
                    "destLat" => $item["dest_lat"],
                    "destLng" => $item["dest_lng"],
                    "distance" => $item["distance"],
                    "approveDate" => $item["approve_date"],
                    "status" => $item["status"],
                    "approveNote" => $item["approve_note"],
                    "remark" => $item["remark"],
                );
            
            }, $responseData);
            // array_push($responseData, Array("masterOrigin"=>"test"));
            return Array(
                "jobRoutes" => $responseData, 
                "masterOrigin"=>Array(
                    "originPlace" => "Head office",
                    "originLat" => 13.914000764877743, 
                    "originLng" => 100.52995738354491,
                ),
                "timestamp" => date("Y-m-d H:i:s")

            );//, (Array("masterOrigin"=>"test"));
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function updateJobRoute($company, $condition, $updateData){
        // $emptyCond = true;
        // $mappingCondition = Array();
        // foreach($condition as $elCond){ 
        //     $itemCond = Array();
        //     foreach($elCond as $key=>$value) {
        //         echo $key." => ".$value;
        //         if($key == "recId") {
        //             $itemCond["rec_id"] = $value;
        //             $emptyCond = false;
        //         }
        //     }
        //     array_push($mappingCondition, $itemCond);
        // }

        // print_r($condition);
        // if(emptyCond) {
        //     return Array("error"=>$strErrorDesc);
        // }

        // $mappingUpdate = Array();
        try {
        //     $mappingUpdate = Array();
        //     // if(isset($updateData["employeeId"])){
        //     //     $mappingUpdate["employee_id"] = $updateData["employeeId"];
        //     // }
        //     if(isset($updateData["routingDate"])){
        //         $mappingUpdate["routing_date"] = $updateData["routingDate"];
        //     }
        //     if(isset($updateData["originPlace"])){
        //         $mappingUpdate["origin_place"] = $updateData["originPlace"];
        //     }
        //     if(isset($updateData["originLat"])){
        //         $mappingUpdate["origin_lat"] = $updateData["originLat"];
        //     }
        //     if(isset($updateData["originLng"])){
        //         $mappingUpdate["origin_lng"] = $updateData["originLng"];
        //     }
        //     if(isset($updateData["destPlace"])){
        //         $mappingUpdate["dest_place"] = $updateData["destPlace"];
        //     }
        //     if(isset($updateData["destLat"])){
        //         $mappingUpdate["dest_lat"] = $updateData["destLat"];
        //     }
        //     if(isset($updateData["destLng"])){
        //         $mappingUpdate["dest_lng"] = $updateData["destLng"];
        //     }
        //     if(isset($updateData["distance"])){
        //         $mappingUpdate["distance"] = $updateData["distance"];
        //     }            
        //     if(isset($updateData["approveStatus"])){
        //         $mappingUpdate["status"] = $updateData["approveStatus"];
        //     }
        //     if(isset($updateData["approvedBy"])){
        //         $mappingUpdate["approved_by"] = $updateData["approvedBy"];
        //     }
        //     if(isset($updateData["approveNote"])){
        //         $mappingUpdate["approve_note"] = $updateData["approveNote"];
        //     }
            
            $model = new JobRouteModel();
            $responseData = $model->updateJobRoute($company, $condition, $updateData);
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return $responseData;
    }

    public function approveJobRoute($reqParams){
        $strErrorDesc = '';
        try {
            $model = new JobRouteModel();
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
            $responseData = $model->updateJobRoute('', '', $updateRequest);
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return $responseData;
    }
}
