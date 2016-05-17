<?php
/**
 * @author guoyunfeng
 */
class cls_dao_lock extends sys_daoabs 
{
    private static $pid;
    private static $lock_dir = 'tmp/';
    private static $lock_array = array();

    public function __construct()
    {
            parent::__construct();

            self::$pid = getmypid();		
            self::$lock_dir = ROOT_PATH.'/filelock/';
    }
    /*
     * 增加一个事务锁
     */
    public function Lock($club_id,$key, $update_interval = 3)
    {							
            for ($i = 1; $i <= $update_interval * 2; $i++) {					
                    $ret = self::addLock($club_id,$key,$update_interval);
                    if($ret){				
                            return true;
                    }	
                    else{
                            usleep(500000); // wait for 0.5 seconds
                    }	
            }		
    }

    /*
     * 删除一个事务锁
    */
    public function unLock($club_id,$key)
    {
            return self::deleteLock($club_id,$key);
    }


    private static function _getFileName($club_id,$key)
    {				
            return $club_id.$key.'.lock';
    }


private static function addLock($club_id,$key,$lock_time){
            $fileName = self::_getFileName($club_id,$key); 
    $fileHandle = self::_getFileHandle($fileName);
            $lock = flock($fileHandle, LOCK_EX | LOCK_NB);
            if($lock ===  TRUE){
                    self::$lock_array[$fileName] = array('file' => $fileHandle);
                    $content = self::$pid . PHP_EOL . microtime(true). PHP_EOL . $club_id.PHP_EOL . $key . PHP_EOL . $lock_time;
                if(fwrite($fileHandle, $content) === FALSE) {
                            flock($handle, LOCK_UN);    // 释放锁定
                    return false;
                }			 
                fflush($fileHandle);
                    return true;
            }       
            return false;
}

private static function deleteLock($club_id,$key){
    $fileName = self::_getFileName($club_id,$key);
    $lock_file =  self::_getPath($fileName);
            if(!file_exists($lock_file)){
            return true;
    }

    $fileHandle = self::_getFileHandle($fileName);
            $success = flock($fileHandle, LOCK_UN);
            ftruncate($fileHandle, 0);
    fclose($fileHandle);       
    unset(self::$lock_array[$fileName]);
    return true;
}

private static function _getFileHandle($name){		
            $fileHandle = null;
            if(isset(self::$lock_array[$name]))	{
                    $fileHandle = self::$lock_array[$name]['file'];
            }		

    if($fileHandle == null) {
        $fileHandle = fopen(self::_getPath($name), 'c');
    }
    return $fileHandle;
}

private static function _getPath($name)
    {			
            return self::$lock_dir.$name;
    }

    function __destruct()
    {			
            foreach (self::$lock_array as $key => $value) {
                    flock($value['file'], LOCK_UN);
            }		
    }
}