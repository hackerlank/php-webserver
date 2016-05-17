<?php
/***
 * 常驻后台（每分钟调用定时任务）
 *  php cron/main.php "crontype=cmolcron"
 * 
 */
class cron_cmoltimer extends sys_cronabs 
{
	public static $timer_list = array(			
		'matchdrive'=>array('path'=>'matchdrive','cron'=>'* * * * *'),
		'bossauto'=>array('path'=>'bossauto','cron'=>'30-59 21 * * *'),
		'finalsauto'=>array('path'=>'finalsauto','cron'=>'0-59 20 * * 0'),
		'allregion'=>array('path'=>'allregion','cron'=>'* * * * *'),
		'messageDequeue'=>array('path'=>'messageDequeue','cron'=>'* * * * *'),
	);


	public static $timer_list_TEST = array(			
		'matchdrive'=>array('path'=>'matchdrive','cron'=>'* * * * *'),
		'bossauto'=>array('path'=>'bossauto','cron'=>'0-29 * * * *'),
		'finalsauto_1'=>array('path'=>'finalsauto','cron'=>'0-44 */2 * * *'),
		'finalsauto_2'=>array('path'=>'finalsauto','cron'=>'45-59 1,3,5,7,9,11,13,15,17,19,21,23 * * *'),
		'allregion'=>array('path'=>'allregion','cron'=>'* * * * *'),
		'messageDequeue'=>array('path'=>'messageDequeue','cron'=>'* * * * *'),
	);


	public static function getTimerList(){
		if(sys_define::IS_TEST)
        {
            return cron_cmoltimer::$timer_list_TEST;   
        }
        return cron_cmoltimer::$timer_list;
	}
	
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function process($val)
	{
		//脚本超时为无限	
		try {			
			$strTime = date('Y-m-d H:i',time());
			$start = strtotime($strTime);
			$timer_list = cron_cmoltimer::getTimerList();
			foreach( $timer_list as $timer){
				$isrun = $this->isrun($timer,$start,$strTime);
				if($isrun == true)
				{					
					$this->run($timer);
				}
			}
		}
		catch(Exception $e)	{
			sys_log::getLogger()->fatal( sys_log::format('cron_cmoltimer',array('Exception_Message'=>$e->getMessage() )));			
		}
	}
	
	function isrun($timer,$start,$strTime)
	{	
		if($timer['cron'] === '* * * * *'){
			return true;
		}
		$next = sys_cronParse::next($timer['cron'], $start - 60);
		$strnext = date('Y-m-d H:i',$next);
		if($strTime == $strnext){
			return true;
		}
		return false;
	}
	
	function run($timer)
	{	
		$key = "crontype=".$timer['path'];
 		$ret = $this->isLock($key); //相同参数的进程，只能运行一个
		if($ret === false){
			$fname = ROOT_PATH . '/log/' . "cmoltimer" . date("Ymd",time()) . '.log';		
			//echo 'php '.ROOT_PATH."/cron/main.php \"{$key}\" &";		
			$out = popen( '/usr/local/php/bin/php '.ROOT_PATH."/cron/main.php \"{$key}\" >> {$fname} &", "r");
			pclose($out);
		} 
		sys_log::getLogger()->info( sys_log::format('cron_cmoltimer',array('is_run'=>$ret?0:1,'run_path'=> $timer['path'])));	
	}
	
}