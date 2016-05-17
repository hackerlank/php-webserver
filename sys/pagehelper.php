<?php
/**
/**
 *
 * @package sys
 */
class sys_pagehelper {
	
	/*
	  $sub_pages;//每次显示的页码数  eg：6 => [1][2][3][4][5][6]
	  $pageNums;//总页数  
	  $current_page;//当前被选中的页  
   */
	public static function construct_num_Page($pageNums,$sub_pages,$current_page){
		if($pageNums < $sub_pages){//‘总页数’小于‘每次显示的页数’
			$current_array=array();  
			for($i=0;$i < $pageNums;$i++){
				$current_array[$i]=$i+1;  
			}
		}else{//‘总页数’大于等于‘每次显示的页数’
			for($i=0;$i < $sub_pages;$i++){
				$page_array[$i]=$i;  
			}
			$current_array=$page_array;
			if($current_page <= floor($sub_pages/2)){ //如果‘当前选中的页码’小于等于 （‘每次显示的页数’除以2）取整 // [1-3]
				for($i=0;$i < $sub_pages;$i++){
					$current_array[$i]=$i+1;  
				}
			}elseif ($current_page <= $pageNums && $current_page > ($pageNums - $sub_pages + 1) ){ //如果‘当前选中的页码’小于等于总页数  //$pageNums - $sub_pages + 1 // (9,14]
				for($i=0;$i< $sub_pages;$i++){  
					$current_array[$i]=($pageNums)-($sub_pages)+1+$i; //floor($sub_pages/2)+$i;  
				}
			}else{
				for($i=0;$i< $sub_pages;$i++){ // [4,9]
					$current_array[$i]=$current_page-2+$i;  
				}
			}
		}
		//var_dump($current_array);echo "<br><br>";
		return $current_array;  
	}
}