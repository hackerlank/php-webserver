<?php
/**
 * html视图类
 * @package cls_view
 * 
 */

class cls_view_html implements sys_viewinterface {
	private static $smarty = null;
	private $fileName;
	private $var;
	
	public function __construct($fileName, $var) {
		$this->fileName = $fileName;
		$this->var = $var;
	}
	
	/**
	 * 显示
	 *
	 */
	function display() {
		header ( "Content-Type:text/html; charset=utf-8" );
		
		$smarty = self::getSmarty();
		$smarty->assign($this->var);
		$smarty->display($this->fileName);
	}

	public static function  static_url($params) {
		return $_SERVER['static'].$params['url'].'?v='.$_SERVER['version'];
	}
	
    private static function getSmarty()
    {
        if(!self::$smarty)
        {
            // 初始化smarty
         	sys_bootstrap::loadClass( ROOT_PATH.'/lib/smarty/Smarty.class.php' );
            
            $smarty = new Smarty();

            $smarty->left_delimiter = '<{';
            $smarty->right_delimiter = '}>';
            $smarty->caching = false;
            $smarty->compile_check = sys_define::IS_TEST;
            $smarty->cache_dir = ROOT_PATH.'/smarty/cache';
            $smarty->compile_dir = ROOT_PATH.'/smarty/compile';
            $smarty->template_dir = ROOT_PATH.'/template/';
            //$smarty->config_dir = ROOT_PATH.'/smarty/config';           
            $smarty->register_function('static_url',  'cls_view_html::static_url' ,true);            
            $smarty->register_block('t',  'cls_view_html::smarty_block_t');    
            if (sys_define::DEBUG_MODE === false) {
            	$smarty->debugging = false; 
        	}

            self::$smarty = $smarty;
        }
        
        return self::$smarty;
    }
	
    public  function fetch() {
		header ( "Content-Type:text/html; charset=utf-8" );
		
		$smarty = self::getSmarty();
		$smarty->assign($this->var);
		return  $smarty->fetch($this->fileName);
	}


	/**
	 * Replaces arguments in a string with their values.
	 * Arguments are represented by % followed by their number.
	 *
	 * @param	string	Source string
	 * @param	mixed	Arguments, can be passed in an array or through single variables.
	 * @returns	string	Modified string
	 */
	static function  smarty_gettext_strarg($str)
	{
		$tr = array();
		$p = 0;

		for ($i=1; $i < func_num_args(); $i++) {
			$arg = func_get_arg($i);

			if (is_array($arg)) {
				foreach ($arg as $aarg) {
					$tr['%'.++$p] = $aarg;
				}
			} else {
				$tr['%'.++$p] = $arg;
			}
		}

		return strtr($str, $tr);
	}

	/**
	 * Smarty block function, provides gettext support for smarty.
	 *
	 * The block content is the text that should be translated.
	 *
	 * Any parameter that is sent to the function will be represented as %n in the translation text,
	 * where n is 1 for the first parameter. The following parameters are reserved:
	 *   - escape - sets escape mode:
	 *       - 'html' for HTML escaping, this is the default.
	 *       - 'js' for javascript escaping.
	 *       - 'url' for url escaping.
	 *       - 'no'/'off'/0 - turns off escaping
	 *   - plural - The plural version of the text (2nd parameter of ngettext())
	 *   - count - The item count for plural mode (3rd parameter of ngettext())
	 */
	static function  smarty_block_t($params, $text, $smarty, &$repeat)
	{
		if ($repeat) return;

		$text = stripslashes($text);

		// set escape mode
		if (isset($params['escape'])) {
			$escape = $params['escape'];
			unset($params['escape']);
		} else {
			$escape = null;
		}

		// set plural version
		if (isset($params['plural'])) {
			$plural = $params['plural'];
			unset($params['plural']);

			// set count
			if (isset($params['count'])) {
				$count = $params['count'];
				unset($params['count']);
			}
		}

		// use plural if required parameters are set
		if (isset($count) && isset($plural)) {
			$text = ngettext($text, $plural, $count);
		} else { // use normal
			$text = gettext($text);
		}

		// run strarg if there are parameters
		if (count($params)) {
			$text = cls_view_html::smarty_gettext_strarg($text, $params);
		}

		switch ($escape) {
			case 'html':
				$text = nl2br(htmlspecialchars($text));
				break;
			case 'javascript':
			case 'js':
				// javascript escape
				$text = str_replace('\'', '\\\'', stripslashes($text));
				break;
			case 'url':
				// url escape
				$text = urlencode($text);
				break;
			default:
				$text = nl2br($text);
		}

		return $text;
	}
    
}
