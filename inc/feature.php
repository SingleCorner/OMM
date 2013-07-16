<?php
/**
 * 字符过滤
 *
 * @author 陈星宇
 */
function string_filter($string){
	if (preg_match("/[\<\>]+/", $string) !== 0) {
		return false;
	}

	if (preg_match("/(union)|[,='\"]/i", $string) !== 0) {
		return false;
	}
	
	return $string;
}

/**
 * 过滤带非数字字符
 * TODO: 可以用PHP内置的 is_numeric 代替
 */
function num_filter($string) {
	preg_match("/\D/",$string,$num_check);
	if($num_check[0]=="") {
		return $string;
	}
	else{
		return false;
	}
}


/**
 * 内容过滤
 *
 * @author 陈星宇
 */
function content_filter($string) {
	if (preg_match("/[<>]*/", $string) !== 0) {
		return false;
	}
	
	return $string;
}
?>