<?php
/*
 * 用户接口 控制器
 */
class cls_ctrl_interface_init extends cls_ctrl_interface_base
{
    /**
     * 注册逻辑
     * @return string
     */
    public function register()
    {
        $aRetEnum  = cls_config_ErrorCode::$code_enum;
        if(empty($this->data['username']))
        {
            $aReturnData['code'] = $aRetEnum['EN_FAILURE'];
            $aReturnData['message'] = '参数不正确';
            return $aReturnData;
        }
        $aReturnData['code'] = $this->validateName($this->data['username'],$this->iAppId);
        if($aReturnData['code'] == $aRetEnum['EN_SUCESS'])
        {
            $oUserServer =  $this->serviceLocator->getService("user");
            $aData = [
                'appid'    => $this->iAppId,
                'username' => $this->data['username'],
                'nickname' => $this->data['nickname'],
                'password' => $this->data['password']
            ];
            $aReturnData['id'] = $oUserServer->addUser($aData);
            if($aReturnData['id'] == $aRetEnum['EN_FAILURE'])
            {
                $aReturnData['message'] = '注册失败';
            }
            $aReturnData['code'] = $aRetEnum['EN_SUCESS'];
            $aReturnData['message'] = '注册成功';
        }
        else
        {
           $aReturnData['message'] = !empty($aRetEnum[$aReturnData['code']])?$aRetEnum[$aReturnData['code']] : '失败'; 
        }
        return $aReturnData;
    }
    
    /**
     * update user
     * @return string
     */
    public function update()
    {
        $aRetEnum  = cls_config_ErrorCode::$code_enum;
        $aReturnInfo = ['code'=> $aRetEnum['EN_PARAM'],'message'=> $aRetEnum[$aRetEnum['EN_PARAM']]];
        if(empty($this->data['uid']) || empty($this->data['appid']))
        {
            $aReturnInfo['message'] = '缺少uid或者appid参数';
            return $aReturnInfo;
        }
        $oUserServer =  $this->serviceLocator->getService("user");
        if(isset($this->data['nickname']))
        {
            $aData['nickname'] = $this->data['nickname'];
        }
        if(isset($this->data['password']))
        {
            $aData['password'] = $this->data['password'];
        }
        $aReturnInfo['rows'] = $oUserServer->updateUser($aData,$this->data['uid'],$this->iAppId); 
        if($aReturnInfo['rows'] != $aRetEnum['EN_FAILURE'])
        {
            $aReturnInfo['code'] = $aRetEnum['EN_SUCESS'];
            $aReturnInfo['message'] = '修改成功';
        }
        else
        {
            $aReturnInfo['code'] = $aRetEnum['EN_FAILURE'];
            $aReturnInfo['message'] = '修改失败';
        }
        return $aReturnInfo;
    }
    
    /**
     * get user
     * @return string
     */
    public function getuser()
    {
        $aRetEnum  = cls_config_ErrorCode::$code_enum;
        $aReturnInfo = ['code'=> $aRetEnum['EN_PARAM'],'message'=> $aRetEnum[$aRetEnum['EN_PARAM']]];
        if(empty($this->data['uid']) || empty($this->data['appid']))
        {
            $aReturnInfo['message'] = '缺少uid或者appid参数';
            return $aReturnInfo;
        }
        $oUserServer =  $this->serviceLocator->getService("user");
        
        $aReturnInfo['userinfo'] = $oUserServer->getUser($this->data['uid'],$this->iAppId);
        if($aReturnInfo['userinfo'] != $aRetEnum['EN_FAILURE'])
        {
            $aReturnInfo['code'] = $aRetEnum['EN_SUCESS'];
            $aReturnInfo['message'] = '用户信息';
        }
        else
        {
            $aReturnInfo['code'] = $aRetEnum['EN_FAILURE'];
            $aReturnInfo['message'] = '用户不存在';
        }
        return $aReturnInfo;
    }
    
    /**
     * disabled user
     * @return string
     */
    public function disabled()
    {
        $aRetEnum  = cls_config_ErrorCode::$code_enum;
        $aReturnInfo = ['code'=> -1,'message'=> 'param_error'];
        if(empty($this->data['uid']) || empty($this->data['appid']))
        {
            $aReturnInfo['message'] = '缺少uid或者appid参数';
            return $aReturnInfo;
        }
        $oUserServer =  $this->serviceLocator->getService("user");
        
        $aRet = $oUserServer->getUser($this->data['uid'],$this->iAppId);
        if(empty($aRet['id']))
        {
            $aReturnInfo['code'] = $aRetEnum['EN_FAILURE'];
            $aReturnInfo['message'] = '用户不存在';
            return $aReturnInfo;
        }
        
        $aData['disabled'] = 1;
        $aReturnInfo['rows'] = $oUserServer->updateUser($aData,$this->data['uid'],$this->iAppId); 
        if($aReturnInfo['rows'] != $aRetEnum['EN_FAILURE'] || !$aReturnInfo['rows'])
        {
            $aReturnInfo['code'] = $aRetEnum['EN_SUCESS'];
            $aReturnInfo['message'] = '禁用成功';
        }
        else
        {
            $aReturnInfo['code'] = $aRetEnum['EN_FAILURE'];
            $aReturnInfo['message'] = '禁用失败';
        }
        return $aReturnInfo;        
    }
    
    /**
     * 检测名字 
     * @param type $sUserName
     * @param type $iAppId
     * @return type
     */
    protected function validateName($sUserName,$iAppId)
    {
        $aRetEnum = cls_config_ErrorCode::$code_enum;
        $sPattern = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9\s]+$/u';
        if(!preg_match($sPattern, $sUserName))
        {
            return $aRetEnum['EN_REGISTER_NAME_ERROR'];
        }

        $oKeywordsService = $this->serviceLocator->getService('keywords');
        if($oKeywordsService->isContainKeywords($sUserName))
        {
            return $aRetEnum['EN_REGISTER_NAME_EXIST'];
        }
        
        $oUserServer =  $this->serviceLocator->getService("user");
        if($oUserServer->findUserByName($sUserName,$iAppId))
        {
            return $aRetEnum['EN_REGISTER_NAME_EXIST'];
        }
        return $aRetEnum['EN_SUCESS'];
    }
	
}