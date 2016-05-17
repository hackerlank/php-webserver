<?php
/***
 * 常驻后台（每分钟调用定时任务）
 *  php cron/main.php "crontype=cmolcron"
 * 
 */
class cron_cmolcron extends sys_cronabs
{
	public static $cron_list = array(
		'ladder_award'=>array('path'=>'ladderaward','cron'=>'*/10 * * * *'),		//每10分钟执行一次
		'repair_skillsoul'=>array('path'=>'repairskillsoul','cron'=>'*/30 * * * *'),		//每30分钟执行一次
		'calculate_endless_rank'=>array('path'=>'calculateendlessrank','cron'=>'10,20,30,40,50 * * * *'),//每小时的10,20,30,40,50执行
		'endless_award'=>array('path'=>'endlessaward','cron'=>'59 23 * * *'),//每天23:59执行
	);


	public static $cron_list_TEST = array(			
		'ladder_award'=>array('path'=>'ladderaward','cron'=>'*/10 * * * *'),	    //每10分钟执行一次
		'repair_skillsoul'=>array('path'=>'repairskillsoul','cron'=>'*/30 * * * *'),//每30分钟执行一次
		'calculate_endless_rank'=>array('path'=>'calculateendlessrank','cron'=>'10,20,30,40,50 * * * *'),//每小时的10,20,30,40,50执行
		'endless_award'=>array('path'=>'endlessaward','cron'=>'59 23 * * *'),//每天23:59执行
	);


	public static function getCronList(){
		if(sys_define::IS_TEST)
        {
            return cron_cmolcron::$cron_list_TEST;   
        }
        return cron_cmolcron::$cron_list;
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
			$cron_list = cron_cmolcron::getCronList();
			foreach( $cron_list as $cron){					
				$next = sys_cronParse::next($cron['cron'], $start - 60);
				$strnext = date('Y-m-d H:i',$next);
				if($strTime == $strnext)
				{					
					$this->run($cron);
				}
			}
		}
		catch(Exception $e)	{
			sys_log::getLogger()->fatal( sys_log::format('cron_cmolcron',array('Exception_Message'=>$e->getMessage() )));
		}
	}
	
	function run($cron)
	{		
		$fname = ROOT_PATH . '/log/' . "cmolcron" . date("Ymd",time()) . '.log';		
		echo '/usr/local/php/bin/php '.ROOT_PATH."/cron/main.php \"crontype={$cron['path']}\" &";		
		$out = popen( '/usr/local/php/bin/php '.ROOT_PATH."/cron/main.php \"crontype={$cron['path']}\" >> {$fname} &", "r");
		pclose($out);

		sys_log::getLogger()->info( sys_log::format('cron_cmolcron',array('run'=>$cron['path'] )));
	}
	
}