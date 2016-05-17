<?php
/**
 * exception基础类
 * @package sys
 * 
 */
abstract class sys_exceptionabs extends Exception {
	
	/**
	 * Generally, thie message key is actually english text that extract from source code of php 
	 * 通常,消息键实际上是从PHP源代码提取出来的英文文本.
	 * 
	 * @var string
	 */
	private $messageKey;
	
	/**
	 * The reason that the exception was thrown.
	 *
	 * @var Exception
	 */
	private $cause;


	/**
	 * Get the key of message
	 * 取得Message的键
	 *
	 * @return string
	 */
	public function getMessageKey()
	{
		return $this->messageKey;
	}


	/**
	 * The constructor
	 * 构造函数
	 *
	 * @param string $messageKey message的键
	 * @param string $defaultMessage 默认信息
	 * @param Exception $cause 产生原因
	 */
	public function __construct($messageKey, $defaultMessage = '', $params = null, Exception $cause = null)
	{
		$this->messageKey = $messageKey;
		$this->cause = $cause;
		
		/*$i18n = I18n::getInstance(I18n::DOMAIN_EXCEPTION);
			$message = $i18n->_($messageKey, $params);
			
			if($message == $messageKey && !empty($defaultMessage))
			{
				$message = $defaultMessage;
			}*/
		$message = $messageKey;
		
		parent::__construct($message);
	}


	public function __toString()
	{
		return parent::__toString();
	}

}
