<?php
class ConfigController extends BaseController
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

}
