<?php
/**
 * 基于队列的异步请求
 * @depend class cls_config_request $aRequest
 * @depend class lib_curl_rollcurl
 * @depend class sys_pheanstalkPool
 */
class lib_request_mq
{
    private $_rollCurl = null;
    private $_requestConf = null;
    private $_data = [];
    
    private $_pheanstalkService = null;
    private $_pheanstalk = [];
    
    public function __construct($sCallBack =null) 
    {
        $this->_pheanstalkService = sys_pheanstalkPool::getInstance();
        $this->_rollCurl = new lib_curl_rollcurl($sCallBack);
        $this->_requestConf = cls_config_request::$aRequest;
    }
    
    public function url($sAppName,$sIdentify,$aData =[])
    {
        if(!empty($this->_requestConf[$sAppName][$sIdentify]['url']) && !empty($this->_requestConf[$sAppName][$sIdentify]['queue']))
        {
            $aMqConf = $this->_requestConf[$sAppName][$sIdentify]['queue'];
            //mq data
            $aJob['message'] = $aData;
            $aJob['tube'] = $aMqConf['tube'];
            $aJob['url'] = $this->_requestConf[$sAppName][$sIdentify]['url'];
            
            $this->_pheanstalk[$sAppName.$sIdentify] = $this->getPheanstalk($aMqConf['host'], $aMqConf['port'], $aMqConf['tube']);
            $this->_data[$sAppName.$sIdentify] = $aJob;
        }
        return false;
    }
    
    public function execute()
    {
        if(!empty($this->_data))
        {
            foreach ($this->_data as $sIdent=>$aData)
            {
                if(!empty($this->_pheanstalk[$sIdent]))
                {
                    $sData = $this->packData($aData);
                    $this->_pheanstalk[$sIdent]->put($sData);
                }
            }
            return true;
        }
        return false;
    }

    private function getPheanstalk($sHost,$iPort,$sTubeName =null)
    {
        $oPheanstalk = $this->_pheanstalkService->getPheanstalk($sHost,$iPort); 
        $sTubeName && $oPheanstalk->useTube($sTubeName);
        return $oPheanstalk;
    }
    
    private function packData($aData)
    {
        $aJob['id'] = rand();
        $aJob['date'] = date('Y-m-d H:i:s');
        $aJob['data'] = $aData;
        $sJob = json_encode($aJob);
        return $sJob;
    }
}