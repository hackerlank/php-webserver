<?php
/**
 * 
 * @author guoyunfeng
 */
class cls_ctrl_interface_loginbase extends cls_ctrl_interface_base 
{
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }
    
    public function checkLogin()
    {
        $aRetEnum  = cls_config_ErrorCode::$code_enum;
        $returnInfo = $this->_checkValid();
        if($returnInfo['code'] != cls_config_ErrorCode::$code_enum['EN_SUCESS'])
        {
            $returnInfo = ['code'=> $aRetEnum['EN_SUCESS'],'message'=> 'temp user'];
            //访问ucenter，验证登录状态
            $oRollCurl = new lib_curl_rollcurl();
            $oRollCurl->request(cls_config_app::$aUcenter['login']);
            $sRet = $oRollCurl->execute();
            //$sRet = $oRollCurl->wait(10000);
            //保存token到本地memcache中
            if($sRet)
            {
                $returnInfo = ['code'=> $aRetEnum['EN_SUCESS'],'message'=> 'ucenter user'];
                $aRet = json_decode($sRet, TRUE);
                if(!empty($aRet['data']))
                {
                    $returnInfo = ['code'=> $aRetEnum['EN_SUCESS'],'message'=> $aRet['data']['message']];
                    $oUserServer =  $this->serviceLocator->getService("user");
                    $returnInfo['userinfo'] = $oUserServer->setLoginUser($aRet['data']['userinfo']);
                }
            }
            return $returnInfo;
        }
        return $returnInfo;
    }

    private function _checkValid()
    {
        $aRetEnum  = cls_config_ErrorCode::$code_enum;
        $returnInfo = ['code'=> $aRetEnum['EN_PARAM'],'message'=> $aRetEnum[$aRetEnum['EN_PARAM']]];
        if(!isset($this->iAppId) || !isset($this->data['token']))
        {
            $returnInfo['message'] = '缺少appid或者token参数';
            return $returnInfo;
        }
        
        $oUserServer =  $this->serviceLocator->getService("user");
        $aRet = $oUserServer->checkLogin($this->data['token'],$this->iAppId);
        if(empty($aRet))
        {
            $returnInfo = ['code'=> $aRetEnum['EN_FAILURE'],'message'=> '无效token'];
            return $returnInfo;
        }
        
        $returnInfo['code'] = $aRetEnum['EN_SUCESS'];
        $returnInfo['message'] = "success";
        $returnInfo['userinfo'] = $aRet;
        return $returnInfo;
    }
}

