<?php

/**
 * 文件不允许访问
 *
 */
if (!defined('__ROOT__')) {
	header("Status: 404");
	exit;
}


/**
 * 功能索引
 *	
 * 字符过滤 -> 过滤数据库的限制字符
 * 职位标签转换 -> 将职位代码转换为文字
 * 后台权限检测 -> 判断用户是否拥有后台权限
 * 后台模块权限验证 -> 加载模块时验证权限
 */


/**
 * 字符过滤
 *
 * param $string -> 传入的字符串
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
 * 职位标签转换
 *
 * param $val_1 -> 部门
 * param $val_2 -> 职位
 */
function job_converter($val_1,$val_2) {
	switch ($val_1) {
		case "0":
			$result = "系统 ";
			switch ($val_2) {
				case "0":
					$result .= "管理员";
					break;
			}
			break;
		case "1":
			$result = " ";
			switch ($val_2) {
				case "0":
					$result .= "经理";
					break;
				case "1":
					$result .= "助理";
					break;
			}
			break;
		case "2":
			$result = "人事部 ";
			switch ($val_2) {
				case "0":
					$result .= "经理";
					break;
				case "1":
					$result .= "助理";
					break;
			}
			break;
		case "3":
			$result = "财务部 ";
			switch ($val_2) {
				case "0":
					$result .= "经理";
					break;
				case "1":
					$result .= "助理";
					break;
			}
			break;
		case "4":
			$result = "技术部 ";
			switch ($val_2) {
				case "0":
					$result .= "总监";
					break;
				case "1":
					$result .= "经理";
					break;
				case "2":
					$result .= "系统工程师";
					break;
				case "3":
					$result .= "PC工程师";
					break;
				case "4":
					$result .= "数据库工程师";
					break;
			}
		break;
		case "5":
			$result = "商务部 ";
			switch ($val_2) {
				case "0":
					$result .= "经理";
					break;
				case "1":
					$result .= "助理";
					break;
			}
			break;
		case "6":
			$result = "客户部 ";
			switch ($val_2) {
				case "0":
					$result .= "经理";
					break;
				case "1":
					$result .= "助理";
					break;
			}
			break;
	}

	return $result;
}



/**
 * 后台权限检测
 *
 * param $policy -> 获取的用户权限
 * 用户拥有多个权限时，以最高权限为准
 */
function check_policy() {
	$policies = explode('|' , $_SESSION['policy']);
	foreach ($policies as $value) {
		$policy = explode('_' , $value);
		if (in_array("SU",$policy)) {
			$mgr_auth = "pass";
		} else if (in_array("MGR",$policy)) {
			$mgr_auth = "pass";
		} else if (in_array("TMGR",$policy)) {
			$mgr_auth = "pass";
		}
	}
	if ($mgr_auth == "pass") {
		return true;
	} else {
		return false;
	}
}

/**
 * 后台模块权限验证
 *
 * param $module_name -> 需要验证的模块名
 */
 function is_policy($module_name) {
	$policies = explode('|', $_SESSION['policy']);
	$attr = explode('_' , $module_name);		//权限数组化，遍历主权限与模块权限
	foreach($policies as $value) {
		$policy = explode('_' , $value);
		switch($policy[0]) {
			case "SU":
				$module_auth = "pass";
				break;
			case "MGR":
				if ($policy[1] == $attr[0]){
					$module_auth = "pass";
				}
				break;
			case "TMGR":
				if ($policy[1] == $attr[0] && strtotime($_SESSION['policy_time']) > time()) {
					$module_auth = "pass";
				} else {
					//保留代码，用于日后进行单个模块的权限控制
				}
				break;
			default:
				break;
		}
	}
	if ($module_auth == "pass") {
		return true;
	} else {
		return false;
	}
 }



?>