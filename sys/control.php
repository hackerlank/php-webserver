<?php
/**
 * 控制类
 * @package sys
 * 
 */

class sys_control {

    /**
     * 让客户端不缓存页面
     *
     */
    public static function noCache()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0",false);
        header("Pragma: no-cache");
    }

    /**
     * 国际化
     * 
     */
    public static function setLocate($lang=null)
    {
        if(empty($lang)){
                $lang = $_SERVER['lang'];
        }

        $locale = $lang.".utf8";
        setlocale(LC_ALL, $locale); 
        putenv("LC_ALL=$locale");
        bind_textdomain_codeset(sys_define::PROJECT_LOCALE_NAME, 'UTF-8');
        bindtextdomain(sys_define::PROJECT_LOCALE_NAME, sys_define::PROJECT_LOCALE);
        textdomain(sys_define::PROJECT_LOCALE_NAME);
    }


    /**
     * 解析URL，转到对应的控制类去处理
     *
     */
    public static function performAction()
    {
        $arr = array();
        if (isset($_POST['act'])) {
                $act = $_POST['act'];
        } elseif (isset($_GET['act'])) {
                $act = $_GET['act'];
        } elseif (isset ( $_GET ['cmd'] )){	
                sys_control::performInterface();
                return;
        }
        else
        {
                return;
        }

        if (empty($act) || ! preg_match('/^([0-9a-z]+)\.([0-9a-z_]+)$/i',$act,$arr)) {
                $arr [0] = 'index.main';
                $arr [1] = 'index';
                $arr [2] = 'main';
        }

        $fname = ROOT_PATH . '/cls/ctrl/' . $arr [1] . '.php';
        if (!sys_bootstrap::loadClass($fname)) {
            sys_log::getLogger()->fatal( sys_log::format('control',array('act'=>$act,'msg'=>'unsupport')));
            if(sys_define::IS_TEST)
            {
                throw new cls_exception_support("e.ctrl.request.invalid");
            }			
            return;
        }

        $clsname = 'cls_ctrl_' . $arr [1];
        $ctrl = new $clsname();
        $view = $ctrl->$arr [2]();

        if ($view) {
                $view->display();
        }	
    }

    public static function performInterface()
    {
        $cmd = isset ( $_GET ['cmd'] ) ? $_GET ['cmd'] : null;	
        $arr = array();
        if (empty($cmd) || ! preg_match('/^([0-9a-z]+)\.([0-9a-z_]+)$/i',$cmd,$arr)) {
                $arr [0] = 'index.main';
                $arr [1] = 'index';
                $arr [2] = 'main';
        }

        $returnInfo = array('ret'=>cls_config_ErrorCode::$code_enum['EN_SUCESS']);
        $retdata = null;

        $fname = ROOT_PATH . '/cls/ctrl/interface/' . $arr [1] . '.php';
        if (sys_bootstrap::loadClass($fname)) {
                $clsname = 'cls_ctrl_interface_' . $arr [1];
                $ctrl = new $clsname();			
                $ctrl->StartLog();
                $retdata = $ctrl->$arr [2]();
                $ctrl->EndLog();
        } else {
                sys_log::getLogger()->fatal( sys_log::format('control',array('act'=>$act,'msg'=>'unsupport')));
                $returnInfo['ret'] = cls_config_ErrorCode::$code_enum['EN_CMD_CLASSFAILURE'];
        }		

        $result = sys_control::_makeResult($returnInfo, $retdata);
        if ($result) {
                $view = new cls_view_json($result);
                $view->display();
        }	
        exit();
    }



    /**
     * 定义返回值
     */
    private static function _makeResult($returnInfo, $data=array())
    {
        $ret_enum = cls_config_ErrorCode::$code_enum;
        $params = array(
                        'ret'=>(int)$returnInfo['ret'],
                        'message'=>$ret_enum[$returnInfo['ret']],						
                        'data'=>$data,
                        'server_time'=>time()
        );
        return $params;
    }

    public static function performCtrl($ctrlname, $actname, $params=array())
    {
        $clsname = 'cls_ctrl_' . $ctrlname;
        $ctrl = new $clsname();
        $view = $ctrl->$actname($params);
        if ($view) {
                $view->display();
        }
    }

    /**
     * 
     * 生成返回值
     * ret=0错误1成功
     * @param unknown_type $status
     * @param unknown_type $message
     * @param unknown_type $data
     */
    public static function makeResult($code, $message="", $data=array())
    {
        return array(
                'code'=>$code,
                'message'=>$message,
                'data'=>$data,
        );
    }

    /**
     * 默认中断处理程序
     *
     * @param Exception $exception 中断
     */
    public static function exceptionHandler(Exception $exception)
    {
        $logParams = array('message'=>$exception->getMessage(),'code'=>$exception->getCode(),'file'=>$exception->getFile(),'line'=>$exception->getLine() );	    	    
        if (sys_define::DEBUG_MODE) {
                $logParams['trace'] = $exception->getTrace();
                $logParams['traceString'] = $exception->getTraceAsString();
                echo '<pre>';
                print_r($logParams);
                echo '</pre>';	
        } 
        sys_log::getLogger()->fatal( sys_log::format('exception',$logParams));
    }

    /**
     * 默认中断处理程序
     *
     * @param Exception $exception 中断
     */
    public static function error_handler ($level, $message, $file, $line, $context) {
        $logParams = array('level'=>$level,'message'=>$message,'file'=>$file,'line'=>$line, 'context'=>$context);
        if (sys_define::DEBUG_MODE) {			
                echo '<pre>';
                print_r($logParams);
                echo '</pre>';	
        } 
        sys_log::getLogger()->fatal( sys_log::format('exception',$logParams));
    }
}

