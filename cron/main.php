<?php
/**
 * 例子 php cron/main.php "crontype=matchresult&match_id=1"
 * match_id 是变量
 */
ini_set('max_execution_time', 0);
define('GLOBAL_CACHE',true);
define('ROOT_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
require( ROOT_PATH.'/sys/bootstrap.php' );

if (substr(php_sapi_name(), 0, 3) !== 'cli') {
	die("This Programe can only be run in CLI mode");
}

$querys = $_SERVER['argv'][1];
$val = sys_utils::convertUrlQuery($querys);

$cronName = "cron_".$val['crontype'];
$cron = new $cronName($argv);
$ret = $cron->Lock($querys); //相同参数的进程，只能运行一个
if($ret === true){
	echo 'main.php run:'.$querys."\n";
	$cron->process($val);
	$cron->unlock($querys);
} else {
	echo 'main.php lock,notrun:'.$querys."\n";
}
