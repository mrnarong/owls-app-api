<?php
require_once PROJECT_ROOT_PATH . "/models/Database.php";
class CommonModel extends Database {
    public function getConfig($reqParams)
    {
        $result = $this->select("SELECT rec_id, config_name, data, REPLACE( merging_config, ' ', '' ) as merging_config, status FROM app_configs  WHERE config_name=?", Array("s", $reqParams["config_name"]));
        if(strlen($result[0]["merging_config"])) {
            // echo "Merged with common.config ".$result[0]["merging_config"];
            $query = "SELECT * FROM app_configs  WHERE config_name IN ('".str_replace(",", "','", $result[0]["merging_config"])."')";
            $mergeConfig = $this->select($query);
            $data = json_decode($result[0]["data"], true);
            foreach($mergeConfig as $item) {
                $arrItems = json_decode($item["data"], true);
                foreach($arrItems as $key=>$value) {
                    $data[$key] = $value;
                }
            }
            $result[0]["data"] = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        return $result;
    }

    public function updateConfig($configName, $updateData){
        $query = "UPDATE app_configs SET data=? ".
        (isset($updateData["status"]) ? ", status=?" : "").
        " WHERE config_name=?";
        $format = "s".(isset($updateData["status"]) ? "i" : "")."s";
        // $updateParams = Array();

        $updateParams = Array($format, json_encode($updateData["configData"]));
        if(isset($updateData["status"])) {
            array_push($updateParams, $updateData["status"]);
        }
        array_push($updateParams, $configName);

        // print_r($updateParams);
        return $this->update($query, $updateParams);
    }

    public function getDocument($company, $employeeId, $recId, $tableName, $dataField)
    {
        try{
        $query = "SELECT $dataField FROM $tableName WHERE company=? AND employee_id=?".(isset($recId)?" AND rec_id=?":"");
        $queryParams = Array("ss".(isset($recId)?"i":""), $company, $employeeId);
        if(isset($recId)) {
            array_push($queryParams, $recId);
        }
        // echo $query;
        $result = $this->select($query, $queryParams);
        // print_r($queryParams);
        // print_r($result);
    } catch(ErrorException $err) {
        // print_r($err);
    }
        return $result;
    }

    
    public function approveRequest($company, $tableName, $reqParams)
    {
        $result = false;
        try{
            $query = "UPDATE $tableName SET status=?, approved_by=?, approve_date=NOW() WHERE company=? AND employee_id=? AND rec_id=?";
            $queryParams = Array("ssssi", $reqParams["status"], $reqParams["approvedBy"], $company, $reqParams["employeeId"], $reqParams["refNo"]);

            // echo $query;
            $result = $this->update($query, $queryParams);
            // print_r($queryParams);
            // print_r($result);
        } catch(ErrorException $err) {
            // print_r($err);
        }
        return $result;
    }

}
?>