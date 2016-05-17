<?php
/**
 * 公用类
 *
 * @package sys
 */
class sys_utils {

	/*
	 * 加密的密钥
	*/
	const SALT='&^%donotdothis$#><:';
	
	/**
	 * 获取客户端IP
	 *
	 * @return string
	 */
	public static function getClientIP()
	{
		if (isset($_SERVER)) {
			if (isset($_SERVER ["HTTP_X_FORWARDED_FOR"])) {
				$realip = $_SERVER ["HTTP_X_FORWARDED_FOR"];
			} else 
				if (isset($_SERVER ["HTTP_CLIENT_IP"])) {
					$realip = $_SERVER ["HTTP_CLIENT_IP"];
				} else {
					$realip = isset($_SERVER ["REMOTE_ADDR"]) ? $_SERVER ["REMOTE_ADDR"] : '127.0.0.1';
				}
		} else {
			if (getenv("HTTP_X_FORWARDED_FOR")) {
				$realip = getenv("HTTP_X_FORWARDED_FOR");
			} else 
				if (getenv("HTTP_CLIENT_IP")) {
					$realip = getenv("HTTP_CLIENT_IP");
				} else {
					$realip = getenv("REMOTE_ADDR");
				}
		}
		
		return addslashes($realip);
	}
	
	public static function convertUrlQuery($query) { 
	    $queryParts = explode('&', $query); 	    
	    $params = array(); 
	    foreach ($queryParts as $param) { 
	        $item = explode('=', $param); 
	        $params[$item[0]] = $item[1]; 
	    } 	    
	    return $params; 
	}


    public static function getUrlQuery() { 
        $queryParts = $_GET;
        unset($queryParts['act']);        
        return http_build_query($queryParts,"","&");
    }

	
	public static function microtimeFloat()
	{
    	list($usec, $sec) = explode(" ", microtime());
    	return ((float)$usec + (float)$sec);
	}
	
    public static function showGameGold($game_gold)
    {
        if ($game_gold == 0) {
            return 0;
        }
        $game_gold_str = "";
        if($game_gold >= 10000)
        {
            $billion = floor($game_gold/10000);
            $game_gold_str .= "{$billion}亿";
            $game_gold = $game_gold%10000;	
        }
        if($game_gold) $game_gold_str .= "{$game_gold}万";
        return $game_gold_str;
    }
    
    /*
     * 参数格式化
     */
    public static function stringFormat($str) 
    {    
    	// replaces str "Hello {0}, {1}, {0}" with strings, based on
    	// index in array
    	$numArgs = func_num_args () - 1;
    
    	if ($numArgs > 0) {
    		$arg_list = array_slice ( func_get_args (), 1 );
    
    		// start after $str
    		for($i = 0; $i < $numArgs; $i ++) {
    			$str = str_replace ( "{" . $i . "}", $arg_list [$i], $str );
    		}
    	}
    
    	return $str;
    }
    
    /**
     *加密
     */
    public static function encrypt($text)
    {
    	return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, self::SALT, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }
    /**
     *解密
     */
    public static function decrypt($text)
    {
         return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, self::SALT, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));         
    }
    /*
     * curl 请求
    */
    public static function curlRequest($requestArgs,$requestUrl)
    {
    	$dataUrlParam = http_build_query($requestArgs,"","&");
    	$url = $requestUrl.$dataUrlParam;
    	$tool = new lib_curl_tool ();
    	return $tool->request ( $url );
    }
    
    public static function getMicrotime(){
    	list($a,$b) = explode(' ',microtime());
    	return $a+$b;
    }
    
    /*
     * 获得星期几
    */
    public static function getDayByNum($num)
    {
    	$day = "Monday";
    	switch($num)
    	{
    		case 0:
    			$day = "Sunday";
    			break;
    		case 1:
    			$day = "Monday";
    			break;
    		case 2:
    			$day = "Tuesday";
    			break;
    		case 3:
    			$day = "Wednesday";
    			break;
    		case 4:
    			$day = "Thursday";
    			break;
    		case 5:
    			$day = "Friday";
    			break;
    		case 6:
    			$day = "Saturday";
    			break;
    		default:
    			$day = "Sunday";
    		break;
    	}
    	return $day;
    }
    
    static public function dump($var, $echo=true, $label=null, $strict=true) {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = "<pre>" . $label . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        }else
            return $output;
    }
    
    //implement the escape() method of JavaScript
	static function unescape($str){
		$ret = ''; 
		$len = strlen($str); 
		for ($i = 0; $i < $len; $i++){ 
			if ($str[$i] == '%' && $str[$i+1] == 'u'){
				$val = hexdec(substr($str, $i+2, 4)); 
				if ($val < 0x7f){
					$ret .= chr($val); 
				}else if($val < 0x800){
					$ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f)); 
				}else{
					$ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f)); 
				}
				$i += 5; 
			} 
			else if ($str[$i] == '%'){ 
				$ret .= urldecode(substr($str, $i, 3)); 
				$i += 2; 
			} 
			else{
				$ret .= $str[$i]; 
			}
		} 
		return $ret; 
	} 
    
	

    


}
?>