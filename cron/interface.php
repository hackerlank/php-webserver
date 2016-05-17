<?php
/**
 * interface queue
 * @depend class sys_pheanstalkPool
 */
//sys_bootstrap::loadClass(ROOT_PATH.'/lib/Pheanstalk/pheanstalk_init.php' );
class cron_interface extends sys_cronabs
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * limit memory 32M
     * @param type $val
     */
    public function process($val)
    {
        //sys_log::getLogger()->fatal(sys_log::format('interface queue start',[]));
        echo "interface queue start\n";
        $interfaceConf = cls_config_interface::$aInterface;
        while (1)
        {
            foreach($interfaceConf as $sAppName=>$aApp)
            {
                foreach ($aApp as $sRequest=>$aRequest)
                {
                    if(!empty($aRequest['queue']))
                    {
                        $this->handle($aRequest['queue']);
                        $iMemory = memory_get_usage();
                        if($iMemory > 32000000)
                        {
                            break;
                        }
                    }
                }
            }
        }
        echo "interface queue end\n";
    }
    
    /**
     * 1ms 时间片
     * @param type $aQueueServer
     * @param type $iTimeout
     * @return type
     */
    private function handle($aQueueServer,$iTimeout =1000)
    {
        $oPheanstalkService = sys_pheanstalkPool::getInstance();
        $oPheanstalk = $oPheanstalkService->getPheanstalk($aQueueServer['host'],$aQueueServer['port'],10); 
        $oHandler = NULL;
        if(is_callable($aQueueServer['tube'].'::runTask'))
        {
            $oHandler = new $aQueueServer['tube'];
        }
        else
        {
            echo "ctrl no function runTask!\n";
            return ;
        }
        
        //start
        $aDoneJobs = [];
        list($iUsec, $iSec) = explode(" ", microtime());
        $iStartTime = ((float)$iUsec + (float)$iSec)*1000000;
        while(1) 
        {
            usleep(2);
            list($iUsec, $iSec) = explode(" ", microtime());
            $iTime = ((float)$iUsec + (float)$iSec)*1000000;
            if(($iTime - $iStartTime) > $iTimeout)
            {
                break;
            }
            
            $oJob = $oPheanstalk->watch($aQueueServer['tube'])->ignore('default')->reserve(2);
            if(!empty($oJob))
            {
                $sJson = $oJob->getData();
                $aData = json_decode($sJson,TRUE);
                $aDoneJobs[] = $aData;
                if(!empty($aData['data']))
                {
                    $bRet = $oHandler->runTask($aData);
                }
                $oPheanstalk->delete($oJob);
            }
        }
        //var_dump($aDoneJobs);
        //$pheanstalk->stats();
    }
}