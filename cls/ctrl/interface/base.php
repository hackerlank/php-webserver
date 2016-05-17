<?php
/**
 * interface 基础控制器
 * @author guoyunfeng
 */
class cls_ctrl_interface_base extends sys_ctrlabs 
{
    protected $data = null;
    
    protected $aParam = null;
    protected $iServerId;
    protected $sServerName;
    protected $sServerKey;
    
    protected $iAppId;
    protected $sAppName;
    protected $sAppKey;
    
    public function __construct()
    {
        parent::__construct();
        $data = isset($_GET['data']) ? $_GET['data'] : null;
        if(!empty($data) && $data !=null && $data !="null")
        {
            $data = base64_decode(urldecode($data));
            $this->data = json_decode($data,true);
        }
        //其他参数
        $this->aParam = !empty($_REQUEST) ? $_REQUEST : null;
        
        $this->initServer();
        $this->initApp();
    }
    
    /**
     * server
     */
    public function initServer()
    {
        $this->iServerId = sys_define::SERVER_ID;
        $this->sServerName = sys_define::SERVER_NAME;
        $this->sServerKey = sys_define::SERVER_KEY;
    }
    
    /**
     * client app
     */
    public function initApp()
    {
        $this->iAppId = $this->data['appid'];
        //$this->sAppName = $this->data['appname'];
        //$this->sAppKey = $this->data['appkey'];
    }
    
    public function StartLog()
    {
        
    }

    public function EndLog()
    {
        
    }

}

