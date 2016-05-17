<?php
/**
 * fastfds 上传类
 */
class lib_fdfs_fastfds
{
    private $oTrackerServer = null;
    private $oStorageServer = null;
    private $sGroupName = '';
    
    private static $oInstance = [];
    
    public static function factory($sGroupName)
    {
        if(empty(self::$oInstance[$sGroupName]))
        {
            self::$oInstance[$sGroupName] = new self($sGroupName);
        }
        return self::$oInstance[$sGroupName];
    }

    protected function __construct($sGroupName)
    {
        $this->sGroupName = $sGroupName;

        $this->connectionTracker();
        $this->connectionStorage();
    }

    protected function connectionTracker()
    {
        $oTrackerServer = fastdfs_tracker_get_connection();//选择一个tracker连接服务器
        if (!fastdfs_active_test($oTrackerServer))
        {
            return $this->halt("fastdfs_active_test errno: " . fastdfs_get_last_error_no() . ", error info: " . fastdfs_get_last_error_info());
        }
        
        return $this->oTrackerServer = $oTrackerServer;
    }
    
    protected function connectionStorage()
    {
//        $oServer = fastdfs_connect_server($this->oTrackerServer['ip_addr'], $this->oTrackerServer['port']);//连接此tracker连接服务器
//        if (!$oServer)
//        {
//            return $this->halt("fastdfs_connect_server errno: " . fastdfs_get_last_error_no() . ", error info: " . fastdfs_get_last_error_info());
//        }
//        if (!fastdfs_active_test($oServer))
//        {
//            return $this->halt("fastdfs_active_test errno: " . fastdfs_get_last_error_no() . ", error info: " . fastdfs_get_last_error_info());
//        }

        //选择一个storge存储服务器
        $oStorageServer = fastdfs_tracker_query_storage_store();
        if (!$oStorageServer)
        {
            return $this->halt("fastdfs_tracker_query_storage_store errno: " . fastdfs_get_last_error_no() . ", error info: " . fastdfs_get_last_error_info());
        }
        //$oStorage['sock'] = $oServer['sock'];
        return $this->oStorageServer = $oStorageServer;
    }
    
    /**
     * 根据文件名上传
     *
     * @param string $sLocalFilename //'nginx-logo.png'
     * @param array $aMetaList  //array('width'=>1024, 'height'=>800, 'font'=>'Aris', 'Homepage' => true, 'price' => 103.75, 'status' => FDFS_STORAGE_STATUS_ACTIVE)
     * @return remote filename
     */
    public function upByFileName($sLocalFilename, $aMetaList =[])
    {
        /**
         * group_name
         * filename
         */
        $aFileInfo = fastdfs_storage_upload_by_filename($sLocalFilename, null, $aMetaList, $this->sGroupName , $this->oTrackerServer, $this->oStorageServer);
        
        if(!empty($aFileInfo))
        {
            //M00/00/00/wKiWDVQRGrKADCcMAAB7UNInADc06003.h
            return $aFileInfo['filename'];
        }
        else
        {
            return $this->halt("fastdfs_storage_upload_by_filename fail :".fastdfs_get_last_error_no() . ", error info: " . fastdfs_get_last_error_info());
        }
    }

    /**
     * 把文件 buff 上传
     *
     * @param string $sFileBuff
     * @param string $sFileExt //txt
     * @param array $aMetaList
     * @return remote filename
     */
    public function upByBuff($sFileBuff, $sFileExt, $aMetaList=[])
    {
        if(!empty($sFileBuff))
        {
            $aFileInfo = fastdfs_storage_upload_by_filebuff($sFileBuff,$sFileExt,$aMetaList,$this->sGroupName,$this->oTrackerServer, $this->oStorageServer);
            
            if(!empty($aFileInfo))
            {
                //M00/00/00/wKiWDVQRGrKADCcMAAB7UNInADc06003.h
                return $aFileInfo['filename'];
            }
        }
        return $this->halt("fastdfs_storage_upload_by_filename fail :".fastdfs_get_last_error_no() . ", error info: " . fastdfs_get_last_error_info());      
    }

    /**
     * 下载到本地文件
     *
     * @param string $sRemoteFilename 远程文件名
     * @param string $sLocalFilename 本地文件名
     * @return bool
     */
    public function downToFile($sRemoteFilename, $sLocalFilename)
    {
        if(fastdfs_storage_file_exist($this->sGroupName,$sRemoteFilename,$this->oTrackerServer, $this->oStorageServer))
        {
            $iRet = fastdfs_storage_download_file_to_file($this->sGroupName, $sRemoteFilename, $sLocalFilename, 0, NULL, $this->oTrackerServer, $this->oStorageServer);
            if ($iRet==1)
            {
                return true;
            }
            else
            {
                return $this->halt("fastdfs_storage_upload_by_filename fail :".fastdfs_get_last_error_no() . ", error info: " . fastdfs_get_last_error_info());
            }
        }
        return $this->halt("fastdfs_storage_upload_by_filename fail :".fastdfs_get_last_error_no() . ", error info: " . fastdfs_get_last_error_info());
    }
    
    /**
     * 
     * @param type $sRemoteFilename
     */
    public function getFileId($sRemoteFilename)
    {
        //$remote_filename = 'M00/00/00/fWaowEoaQj8AAFMnYnV13w36.php';
        #####################################
        $sFileId = $this->sGroupName . FDFS_FILE_ID_SEPERATOR . $sRemoteFilename;
        return $sFileId;
    }
    /**
     * 下载到 buff 
     *
     * @param string $sRemoteFilename 远程文件名
     * @return file name
     */
    public function downToBuff($sRemoteFilename)
    {
        $sFileId = $this->getFileId($sRemoteFilename);
        $sFileContent = fastdfs_storage_download_file_to_buff1($sFileId,0,null,$this->oTrackerServer, $this->oStorageServer);
        return $sFileContent;
    }

    /**
     * 删除文件
     *
     * @param string $sRemoteFilename 远程文件名
     * @return bool
     */
    public function delFile($sRemoteFilename)
    {
        $iRet = fastdfs_storage_delete_file($this->sGroupName, $sRemoteFilename, $this->oTrackerServer,$this->oStorageServer);
        if ($iRet==1)
        {
            return true;
        }
        else
        {
            return $this->halt("fastdfs_storage_upload_by_filename fail :".fastdfs_get_last_error_no() . ", error info: " . fastdfs_get_last_error_info());
        }
    }
    
    public function getVersion()
    {
        return fastdfs_client_version();
    }

    protected function halt($sMsg)
    {
        sys_log::getLogger()->fatal(sys_log::format('FastFds Error:',['message'=>$sMsg]));
        return false;
    }

    public function disconnection()
    {
        fastdfs_tracker_close_all_connections();
    }

    public function __destruct()
    {
        $this->disconnection();
    }
        
}

?>


<?php
/*
//download
echo FastDFS::factory('group1')->downToFile('M00/00/00/fWaowEoaUtwAADwWZmBRJw73.png', 'nginx.png'), "<br />";
echo FastDFS::factory('group1')->downToFileEx('M00/00/00/fWaowEoaUtwAADwWZmBRJw73.png'), "<br />";
echo FastDFS::factory('group1')->downToBuff('M00/00/00/fWaowEoaUtwAADwWZmBRJw73.png'), "<br />";

//delete 
FastDFS::factory('group1')->delFile('M00/00/00/fWaowEoaUtwAADwWZmBRJw73.png');

//upload 
echo FastDFS::factory('group1')->upByFileName('nginx.png'), "<br />";
echo FastDFS::factory('group1')->upByBuff( file_get_contents('nginx.png'), 'png' ), "<br />";
*/