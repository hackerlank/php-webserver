#!/bin/env php
<?php
/** A example denoted muti-process application in php
* @filename fork.php
* @touch date Wed 10 Jun 2009 10:25:51 PM CST
* @author Laruence<laruence@baidu.com>
* @license http://www.zend.com/license/3_0.txt PHP License 3.0
* @version 1.0.0
*/
 
/** 确保这个函数只能运行在SHELL中 */
if (substr(php_sapi_name(), 0, 3) !== 'cli') {
    die("This Programe can only be run in CLI mode");
}
 
/** 关闭最大执行时间限制, 在CLI模式下, 这个语句其实不必要 */
set_time_limit(0);
 
$pid = posix_getpid(); //取得主进程ID
$user = posix_getlogin(); //取得用户名

$forktype = array(
	'cmoltimer'=>array('cron'=> 1 ,'sleep'=>2,'crontype'=>'cmoltimer') ,		
        'messageQueue'=>array('cron'=> 1 ,'sleep'=>1,'crontype'=>'messageQueue') ,            
        'cmolcron'=>array('cron'=> 2 , 'cron_time'=>'minute' ,'crontype'=>'cmolcron') ,
);

$querys = $_SERVER['argv'][1];
$val = convertUrlQuery($querys);
$fork = $forktype[$val['crontype']];
if($fork == null)
{
	die("no fork");
}

$fork['val'] = $val;

while (true) { 
	try
	{
                if($fork['cron'] == 1){
                        process_execute($fork);
                        sleep($fork['sleep']);//休息
                }elseif ($fork['cron'] ==2 ) {
                        process_execute($fork);                      
                        sleep(DateDiff($fork['cron_time']));
                }
	}
	catch(Exception $e)	{		
		break;
	}	
}
 
exit(0);
 
function process_execute($fork) {
        $pid = pcntl_fork(); //创建子进程
        if ($pid == 0) {//子进程
                $pid = posix_getpid();
                //echo "* Process {$pid} was created, and Executed:\n";                
                define('GLOBAL_CACHE',false);
                define('ROOT_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
                require( ROOT_PATH.'/sys/bootstrap.php' ); 
                $cronName = "cron_".$fork['crontype'];
                $cron = new $cronName();
                $cron->process($fork['val']);  //解析命令
                exit;
        } else {//主进程
                $pid = pcntl_wait($status, WUNTRACED); //取得子进程结束状态
                if (pcntl_wifexited($status)) {
                        //echo "\n* Sub process: {$pid} exited with {$status}\n";
                }
        }
}


function convertUrlQuery($query) {
	$queryParts = explode('&', $query);
	$params = array();
	foreach ($queryParts as $param) {
		$item = explode('=', $param);
		$params[$item[0]] = $item[1];
	}
	return $params;
}


function DateDiff($cron_time =''){
        $start = time();
        switch ($cron_time) {
                case 'minute':
                        $strTime = date('Y-m-d H:i',$start); 
                        $end = strtotime($strTime) + 60;
                        break;
                case 'hours':
                        $strTime = date('Y-m-d H',$start); 
                        $end = strtotime($strTime) + 3600;
                        break;
                case 'day':
                        $strTime = date('Y-m-d',$start); 
                        $end = strtotime($strTime) + 86400;
                        break;
                default:
                        $strTime = date('Y-m-d H:i',$start); 
                        $end = strtotime($strTime) + 60;
                        break;
        }
        unset($strTime);
        return $end - $start;
}