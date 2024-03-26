<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class ConfigModel extends Database {
    public function getConfig($reqParams)
    {
        return $this->select("SELECT * FROM app_configs  WHERE config_name=?", Array("s", $reqParams["config_name"]));
    }
}
?>