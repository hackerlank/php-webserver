<?php
/**
 * curl基础类
 * @package lib
 * 
 */

class lib_curl_tool {

	private $timeout=30;
	
	public function __construct()
	{
	}

	public function __destruct()
	{
	}
	
	public function request($url, $data = null, $type='POST', $referer = null)
	{
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_URL, $url);
		if ($data) {
			if($type=='POST'){
				curl_setopt($ch,CURLOPT_POST,1);
				curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
			}else{
				curl_setopt($ch,CURLOPT_HTTPGET,1);
			}
		}
		if ($referer) curl_setopt($ch,CURLOPT_REFERER, $referer);
		curl_setopt($ch,CURLOPT_TIMEOUT, $this->timeout);
		ob_start();
		curl_exec($ch);
		$contents = ob_get_contents();
		ob_end_clean();
		curl_close($ch);
		
		return $contents;
	}
	
}
