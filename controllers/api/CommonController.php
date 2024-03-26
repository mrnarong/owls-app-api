<?php
class CommonController extends BaseController
{
    public function getConfig($reqParams, $encode=true){
        $strErrorDesc = '';
        try {
            $configModel = new ConfigModel();
            // print_r($reqParams);
            $responseData = $configModel->getConfig($reqParams);

        } catch (Error $e) {
            $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
        }
        // send output
        if (!$strErrorDesc) {
            return Array("configName"=>$reqParams["config_name"], "config"=>$encode ? base64_encode($responseData[0]["data"]) : $responseData[0]["data"]);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function updateConfig($configName, $updateData){
        $strErrorDesc = '';
        try {
            $model = new ConfigModel();
            // print_r($reqParams);
            $responseData = $model->updateConfig($configName, $updateData);

        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return $responseData;    
    }
    
    public function approveRequest($company, $reqParams){
        $strErrorDesc = '';
        $mapTableName = Array(
            // "employee"=>Array("table"=>"employees", "folder"=>"employee", "dataField"=>"documents", "multi"=>false),
            // "expense"=>Array("table"=>"expenses", "folder"=>"expenses", "dataField"=>"documents", "multi"=>false),
            // "jobroute"=>Array("table"=>"job_routes", "folder"=>"jobroutes", "dataField"=>"photo", "multi"=>false),
            "leave"=>Array("table"=>"employee_leaves", "multi"=>false),
        );

        try {
            $model = new CommonModel();
            // print_r($reqParams);
            $responseData = $model->approveRequest($company, $mapTableName[$reqParams["type"]]["table"], $reqParams);

        } catch (Error $e) {
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return $responseData;    
    }

    public function getDocument($company, $reqParams){
            $strErrorDesc = '';
        // print_r($reqParams);
        $mapDocuments = Array(
            "employee"=>Array("table"=>"employees", "folder"=>"employee", "dataField"=>"documents", "multi"=>false),
            "expense"=>Array("table"=>"expenses", "folder"=>"expenses", "dataField"=>"documents", "multi"=>false),
            "jobroute"=>Array("table"=>"job_routes", "folder"=>"jobroutes", "dataField"=>"photo", "multi"=>false),
            "leave"=>Array("table"=>"employee_leaves", "folder"=>"leaves", "dataField"=>"documents", "multi"=>false),
        );
        $model = new CommonModel();
        $docResult = $model->getDocument($company, $reqParams["employeeId"], $reqParams["recId"], $mapDocuments[$reqParams["docName"]]["table"], $mapDocuments[$reqParams["docName"]]["dataField"]);
        // print_r($docResult);
        $filePath = "../../../app_data/".$mapDocuments[$reqParams["docName"]]["folder"]."/";
        // echo $filePath;
        $fileList = Array();
        foreach($docResult as $doc) {
            // echo $doc[$mapDocuments[$reqParams["docName"]]["dataField"]];
            $docList = explode(",", str_replace(" ", "", $doc[$mapDocuments[$reqParams["docName"]]["dataField"]]));
            for($idx=0; $idx<sizeof($docList); $idx++) {
                // $fileName = $filePath.$file;
                // echo $reqParams["docIdx"]. " ". $idx."\n";
                if(isset($reqParams["docIdx"])) {
                    if ($reqParams["docIdx"]===$idx) {
                        array_push($fileList, $docList[$idx]);
                        break;
                    }
                } else {
                    array_push($fileList, $docList[$idx]);
                }
            }
            // echo "loop\n";
        }
        
        $responseData = Array();
        foreach($fileList as $fileName) {
            $idxExt = strrpos($fileName, ".");

            $fileExt = $idxExt ? strtolower(substr($fileName, $idxExt+1)) : "";
            $base64 = "";
            // echo $idxExt." ".$fileExt;
            // echo $filePath.$fileName;
            if($idxExt && file_exists($filePath.$fileName)) {
                $dataOk = false;
                switch($fileExt) {
                    case "jpeg": case "jpg":
                        $fileExt = "jpeg";
                    case "png": case "pdf":
                        $dataOk = true;
                        $base64 = base64_encode(file_get_contents($filePath.$fileName));
                }
                // if($fileExt == "jpeg" || $fileExt == "jpg") {
                //     $fileExt = "jpeg";
                //     $dataOk = true;
                // } else if($fileExt == "png" ) {
                //     $dataOk = true;
                // } else if($fileExt == "pdf") {
                //     $dataOk = true;
                //     $base64 = base64_encode(file_get_contents($filePath.$fileName));
                // }
                if($dataOk) {
                    array_push($responseData, Array("fileName"=> $fileName, "fileType"=>$fileExt, "base64"=> $base64));
                }
            } else {
                // document error!
            }
        }

        return $responseData;    
    }


}
