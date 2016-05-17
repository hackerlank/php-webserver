<?php
/**
 * 封装请求对象
 * @depend class cls_config_request $aRequest
 * @depend class lib_curl_rollcurl
 * 
 */
class lib_request_url
{
    private $_asyncRollCurl = null;
    
    private $_syncRollCurl = null;
    
    private $_requestConf = null;
    
    private $_url = [];
    
    public function __construct($sCallBack =NULL) 
    {
        $this->_asyncRollCurl = new lib_curl_rollcurl($sCallBack);
        $this->_syncRollCurl = new lib_curl_rollcurl($sCallBack);
        $this->_requestConf = cls_config_request::$aRequest;
    }
    
    public function url($sAppName,$sIdentify,$aData =[])
    {
        if(!empty($this->_requestConf[$sAppName][$sIdentify]['url']))
        {
            $sMethod = 'GET';
            if(!empty($aData))
            {
                $sMethod = 'POST';
            }
            if(!empty($this->_requestConf[$sAppName][$sIdentify]['async']))
            {
                $this->_url['async'][$sAppName.$sIdentify] = $sAppName.$sIdentify;
                $this->_asyncRollCurl->request($this->_requestConf[$sAppName][$sIdentify]['url'], $sMethod, $aData);
            }
            else
            {
                $this->_url['sync'][$sAppName.$sIdentify] = $sAppName.$sIdentify;
                $this->_syncRollCurl->request($this->_requestConf[$sAppName][$sIdentify]['url'], $sMethod, $aData);
            }
        }
        return false;
    }
    
    public function asyncExec()
    {
        $this->_asyncRollCurl->execute();
        return true;
    }
    
    public function syncExec($iTimeout = 10000)
    {
        if(!empty($this->_url['sync']) && count($this->_url['sync']) <=1)
        {
            return $this->_syncRollCurl->execute();
        }
        else
        {
            $this->_syncRollCurl->execute();
            return $this->_syncRollCurl->wait($iTimeout);
        }
        return false;
    }
    
    public function execute($iTimeout =10000)
    {
        $this->asyncExec();
        return $this->syncExec($iTimeout);
    }
}