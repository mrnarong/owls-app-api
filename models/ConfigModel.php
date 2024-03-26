<?php
require_once PROJECT_ROOT_PATH . "/models/Database.php";
class ConfigModel extends Database {
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

}
?>