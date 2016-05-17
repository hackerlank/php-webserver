<?php
/*
 * 分页样式
 */
class sys_subpages 
{	
	private $each_disNums; //每页显示的条目数  
	private $nums; //总条目数  
	private $current_page; //当前被选中的页  
	private $sub_pages; //每次显示的页数  
	private $pageNums; //总页数  
	private $page_array = array (); //用来构造分页的数组  
	private $page_js_method; //每个分页的链接  
	private $subPage_type; //显示分页的类型  
	/* 
   __construct是SubPages的构造函数，用来在创建类的时候自动运行. 
   @$each_disNums   每页显示的条目数 
   @nums     总条目数 
   @current_num     当前被选中的页 
   @sub_pages       每次显示的页数 
   @subPage_link    每个分页的链接 
   @subPage_type    显示分页的类型 
    
   	当@subPage_type=1的时候为普通分页模式 
         example：   共4523条记录,每页显示10条,当前第1/453页 [首页] [上页] [下页] [尾页] 
         当@subPage_type=2的时候为经典分页样式 
         example：   当前第1/453页 [首页] [上页] 1 2 3 4 5 6 7 8 9 10 [下页] [尾页] 
   */
	public function __construct($each_disNums, $nums, $current_page, $sub_pages, $page_js_method) 
	{
		$this->each_disNums = intval ( $each_disNums );
		$this->nums = intval ( $nums );
		if (! $current_page) 
		{
			$this->current_page = 1;
		} 
		else 
		{
			$this->current_page = intval ( $current_page );
		}
		$this->sub_pages = intval ( $sub_pages );		
		$this->pageNums = ceil ( $nums / $each_disNums );
		$this->page_js_method = $page_js_method;	
	}
	
	/* 
    __destruct析构函数，当类不在使用的时候调用，该函数用来释放资源。 
   */
	function __destruct() {
		unset ( $this->each_disNums );
		unset ( $this->nums );
		unset ( $this->current_page );
		unset ( $this->sub_pages );
		unset ( $this->pageNums );
		unset ( $this->page_array );
		unset ( $this->page_js_method );
		unset ( $this->subPage_type );
	}	
	
	/*
	 *   用来给建立分页的数组初始化的函数。 
	 */
	public function initArray() 
	{
		for($i = 0; $i < $this->sub_pages; $i ++) 
		{
			$this->page_array [$i] = $i;
		}
		return $this->page_array;
	}
	
	/* 
	 *   construct_num_Page该函数使用来构造显示的条目 
	 *   即使：[1][2][3][4][5][6][7][8][9][10]
   	*/
	public function construct_num_Page() 
	{
		if ($this->pageNums < $this->sub_pages) 
		{
			$current_array = array ();
			for($i = 0; $i < $this->pageNums; $i ++) 
			{
				$current_array [$i] = $i + 1;
			}
		} 
		else 
		{
			$current_array = $this->initArray ();
			if ($this->current_page <= 3) 
			{
				for($i = 0; $i < count ( $current_array ); $i ++) {
					$current_array [$i] = $i + 1;
				}
			} 
			elseif ($this->current_page <= $this->pageNums && $this->current_page > $this->pageNums - $this->sub_pages + 1) 
			{
				for($i = 0; $i < count ( $current_array ); $i ++) 
				{
					$current_array [$i] = ($this->pageNums) - ($this->sub_pages) + 1 + $i;
				}
			} 
			else 
			{
				for($i = 0; $i < count ( $current_array ); $i ++) 
				{
					$current_array [$i] = $this->current_page - 2 + $i;
				}
			}
		}
		
		return $current_array;
	}
	
	/*
	 * 	 构造经典模式的分页
	 *  [上页] 1 2 3 4 5 6 7 8 9 10 1/453 [下页]
   */	
	public function subPageCss() 
	{
		$subPageCssStr = "<div class='long_page'>";
		if ($this->current_page > 1) 
		{
			$prewPageUrl = sys_utils::stringFormat($this->page_js_method,$this->current_page - 1);
			$subPageCssStr.='<div id="prev_page" class="button_prevpage"><a href="javascript:void(0)" onclick="{0}">上一页</a></div>';
			$subPageCssStr = sys_utils::stringFormat($subPageCssStr,$prewPageUrl);
		} 
		else 
		{
			$subPageCssStr.='<div id="prev_page" class="button_prevpage_disabled">上一页</div>';
		}
		
		$subPageCssStr.='<div class="numb_page_div1 b4 yuan4 a-center">';
		
		$a = $this->construct_num_Page ();
		for($i = 0; $i < count ( $a ); $i ++) 
		{
			$s = $a [$i];
			if ($s == $this->current_page) 
			{
				$subPageCssStr.='<a class="color_orange1 page_num">'.$this->current_page.'</a>';
				continue;
			}
			$subPageCssStr.='<a class="color_orange1 under page_num" onclick="{0}">{1}</a>';
			$pageStr = sys_utils::stringFormat($this->page_js_method,$s);
			$subPageCssStr = sys_utils::stringFormat($subPageCssStr,$pageStr,$s);	
		}
		
		$subPageCssStr.='</div>';
		$subPageCssStr.='<div class="numb_page_div2 b4 yuan4 a-center">{0}/{1}</div>';
		$subPageCssStr = sys_utils::stringFormat($subPageCssStr,$this->current_page,$this->pageNums);
		
		if ($this->current_page < $this->pageNums) 
		{
			$nextPageUrl = sys_utils::stringFormat($this->page_js_method,$this->current_page + 1);

			$subPageCssStr .= '<div id="next_page" class="button_nextpage"><a href="javascript:void(0)" onclick="{0}">下一页</a></div>';
			$subPageCssStr = sys_utils::stringFormat($subPageCssStr,$nextPageUrl);
		} 
		else 
		{
			$subPageCssStr .= '<div id="next_page" class="button_nextpage_disabled">下一页</div>';		
		}
		return $subPageCssStr;
	}
}
?>