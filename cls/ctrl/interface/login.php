<?php

class cls_ctrl_interface_login  extends cls_ctrl_interface_base 
{
    /**
     * 用户登录
     * @return string
     */
    public function login()
    {
        $aRetEnum  = cls_config_ErrorCode::$code_enum;
        $aReturnData = array('code'=> $aRetEnum['EN_FAILURE'] ,'token'=>null,'uid'=>null,'message'=>null);
        if(empty($this->data['appid']) || empty($this->data['password']) || empty($this->data['appid']))
        {
            $aReturnData['code'] = $aRetEnum['EN_PARAM'];
            $aReturnData['message'] = '参数不正确';
            return $aReturnData;
        }
        $oUserServer =  $this->serviceLocator->getService("user");
        $xRet = $oUserServer->login($this->data['username'],$this->data['password'],$this->data['appid']);
        if(is_numeric($xRet) && $xRet == $aRetEnum['EN_FAILURE'])
        {
            $aReturnData['message'] = '登陆失败';
        }
        else if(is_array($xRet))
        {
            $aReturnData['code'] = $aRetEnum['EN_SUCESS'];
            $aReturnData['message'] = 'success';
            $aReturnData['userinfo'] = $xRet;
        }
        return $aReturnData;
    }
}