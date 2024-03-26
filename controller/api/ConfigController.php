<?php
class ConfigController extends BaseController
{
    public function getConfig($reqParams){
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
            return Array("configName"=>$reqParams["config_name"], "config"=>base64_encode($responseData[0]["data"]));
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

}
