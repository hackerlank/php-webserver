<?PHP
define('CURR_TIME',$_SERVER['REQUEST_TIME']);
!defined('GLOBAL_CACHE') && define('GLOBAL_CACHE',true);	//php全局缓存
class sys_define
{
    // database config
    public static $main_db = array(
            'host' => '192.168.150.13',
            'port' => 3306,
            'user' => 'dev',
            'pass' => 'dev',
            'dbn' => 'ucenter',
        );
    /*
    public static $log_db = array(
            'host' => '192.168.150.13',
            'port' => 3306,
            'user' => 'dev',
            'pass' => 'dev',
            'dbn' => 'fm_log_db',
        );

    public static $system_db = array(
            'host' => '192.168.150.13',
            'port' => 3306,
            'user' => 'dev',
            'pass' => 'dev',
            'dbn' => 'fm_system_db',
        );
    */
    // hash database number
    /*
    const HASH_MAX = 2;
    public static $hash_db = array(
            0 => array('host'=>'10.4.1.200','port'=>3306,'user'=>'tuitui','pass'=>'tuitui','dbn'=>'tuitui_app_'),
            1 => array('host'=>'10.4.1.200','port'=>3306,'user'=>'tuitui','pass'=>'tuitui','dbn'=>'tuitui_app_'),
        );
    */	
    // memcache config
    public static $common_memcache = array(
            0 => array('host' => '192.168.150.13','port' => 11211,'persistent'=>false),
        );

    // memcache config
    public static $login_memcache = array(
            0 => array('host' => '192.168.150.13','port' => 11211,'persistent'=>false),
        );

    //通知服务器
    /*
    public static $notice_server = array(
            'host' => '10.0.0.12',
            'port' => 11300,
            'tube'=> 'testphone'
    );	
    */
    public static $common_redis = array(
            'host' => '192.168.150.13',
            'port' => 6379,
            'select' => 2,
            'password'=>'ztgame@123'
        );
	
    //照片评论队列服务器
    public static $comment_server = array(
            'host' => '192.168.150.13',
            'port' => 11300,
            'tube'=> 'cls_ctrl_interface_push'
    );

    // project name
    const PROJECT_ENV_OUT = false; // 项目在外网环境运行
    const PROJECT_NAME = 'ucenter';	
    const PROJECT_PATH = '/www/ucenter';

    //国际化
    const PROJECT_LOCALE = '/www/ucenter/locale';
    const PROJECT_LOCALE_NAME = 'default';

    // other config
    const DEBUG_MODE = true; // 是否打开调试模式
    const LOG_QUERY = true; // 是否记录数据库操作和memcache操作	

    //log目录
    const LOG_DIR = "/www/ucenter/log";

    // 测试模式
    const IS_TEST = true;
    
    //SERVER 相关
    const SERVER_ID = 1;
    const SERVER_NAME = 'ucenter';
    const SERVER_KEY = '123456789123';//rand
    
}
