<?php
/**
 * queue
 */
sys_bootstrap::loadClass( ROOT_PATH.'/lib/Pheanstalk/pheanstalk_init.php' );
class cron_queue extends sys_cronabs
{
    public function __construct()
    {
            parent::__construct();
    }

    public function process($val)
    {
        echo "pic_comment start\n";

        $aCommentServer = sys_define::$comment_server;
        $oPheanstalkService = sys_pheanstalkPool::getInstance();
        $oPheanstalk = $oPheanstalkService->getPheanstalk($aCommentServer['host'],$aCommentServer['port'],10); 
        //$pheanstalk = new Pheanstalk_Pheanstalk($aCommentServer['host'],$aCommentServer['port'],10);
        $oHandler = NULL;
        if(is_callable(sys_define::$comment_server['tube'].'::runTask'))
        {
            $oHandler = new sys_define::$comment_server['tube'];
        }
        else
        {
            echo "ctrl no function runTask!\n";
            return ;
        }
        
        $aDoneJobs = [];
        while(1) 
        {
            $oJob = $oPheanstalk->watch($aCommentServer['tube'])->ignore('default')->reserve(2);
            if(!empty($oJob))
            {
                $sJson = $oJob->getData();
                $aData = json_decode($sJson,TRUE);
                $aDoneJobs[] = $aData;
                $iMemory = memory_get_usage();
                if($iMemory > 1000000) 
                {
                    break;
                }
                if(count($aDoneJobs) > 1000)
                {
                    break;
                }
                if(!empty($aData['data']))
                {
                    $oHandler->runTask($aData);
                }
                $oPheanstalk->delete($oJob);
            }
            usleep(5);
        }
        var_dump($aDoneJobs);
        //
        //$pheanstalk->stats();

        echo "pic_comment end\n";
    }
}