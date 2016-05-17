<?php
/**
 * 
 * sys_serialization
 * @author guoyunfeng
 */
class sys_serialization
{	
	static $_serialize_type = null;

	/*
		序列化
	*/
    public static function serialize($value,$serializeType = null)
    {
    	if($serializeType == null)
    		$serializeType = self::getSerializeType();
    	
		switch ($serializeType) {
    		case 'msgpack':
    			return msgpack_pack($value);	
    		case 'json':
    			return json_encode($value);    			
    		default:
    			return $value;
    	}
	}

	/*
	 反序列化
	*/
    public static function unserialize($value,$serializeType = null)
    {    	
    	if($serializeType == null){
    		$serializeType = self::getSerializeType();
        }
    	switch ($serializeType) {
    		case 'msgpack':    			
    			return  msgpack_unpack($value);  
    		case 'json':{  
    			if(!is_string($value))
                    return null;
    			return json_decode($value, true);  
    		}
    		default:
    			return $value;    		
    	}
	}

	/*
		序列化类型
	*/
	public static function getSerializeType()
	{
		if(self::$_serialize_type != null)
			return self::$_serialize_type;
     
		self::$_serialize_type = 'json';
        if(function_exists('msgpack_pack')) {
            self::$_serialize_type = 'msgpack';
        }     
        return self::$_serialize_type;
	}
}
?>