<?php
/***
 * 天梯赛奖励
* @author yandean
*/
class cron_apinotice extends sys_cronabs
{
	public function __construct()
	{
		parent::__construct();
	}

	public function process($val)
	{
		echo "api_notice start\n";

		sys_bootstrap::loadClass( ROOT_PATH.'/lib/Pheanstalk/pheanstalk_init.php' );
		$notice_server = sys_define::$notice_server;	
		$pheanstalk = new Pheanstalk_Pheanstalk($notice_server['host'],$notice_server['port'],10);

// ----------------------------------------
// producer (queues jobs)

//$pheanstalk->useTube('testtube')->put("job payload goes here\n");

// ----------------------------------------
// worker (performs jobs)

echo "begin\n";

$job = $pheanstalk
  ->watch('testtube')
  ->ignore('default')
  ->reserve(2);

echo "end\n";
var_dump($job);
//echo $job->getData();

//$pheanstalk->delete($job);

// ----------------------------------------
// check server availability
echo "3333\n";
var_dump($pheanstalk->stats()); // true or false


	}
}