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
 * 1.1	过滤 -> 注入检测
 * 1.2	过滤 -> 数字检测
 * 1.3	过滤 -> XSS检测
 * 
 * 2.1	转换 -> 职位代码转换
 * 2.2	转换 -> 性别代码转换
 * 2.3	转换 -> 账号状态转换
 *
 * 3.1	用户端 -> 模块检测
 *
 * 4.1	管理端 -> 访问权限检测
 * 4.2	管理端 -> 模块权限检测
 */


/**
 * 1.1	过滤 -> 注入检测
 *
 * param $string -> 传入的字符串
 */
function string_filter($string) {
	if (preg_match("/[\<\>]+/", $string) !== 0) {
		return false;
	}
	if (preg_match("/(union)|[,='\"]/i", $string) !== 0) {
		return false;
	}
	
	return $string;
}


/**
 * 1.2	过滤 -> 数字检测
 *
 * param $string -> 传入的数字字符串
 */
function numeric_filter($string) {
	if (is_numeric($string)) {
		return $string;
	} else {
		return false;
	}
	
}


/**
 * 1.3	过滤 -> XSS检测
 *
 * param $string -> 传入的数字字符串
 */
function XSS_filter($string) {
	if (preg_match("/(<script>)/", $string) !== 0) {
		return false;
	}	
	return $string;
}


/**
 * 2.1	转换 -> 职位代码转换
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
 * 2.2	转换 -> 性别代码转换
 *
 * param $val -> 性别代码
 */
function gender_converter($val) {
	switch($val) {
		case "0":
			return "女";
			break;
		case "1":
			return "男";
			break;
		default:
			return "N/A";
			break;
	}
}


/**
 * 2.3	转换 -> 账号状态转换
 *
 * param $val -> 状态代码
 */
function status_converter($val) {
	switch($val) {
		case "0":
			return "冻结";
			break;
		case 1:
			return "激活";
			break;
		default:
			return "N/A";
			break;
	}
}


/**
 * 3.1	用户端 -> 模块检测
 *
 * param $val -> 用户所属部门
 */
function module_usercheck($val) {
	switch ($val) {
		case "0":
			$url = array(
				"修改密码" => "chgpasswd",
			);
			break;
		case "1":
			break;
		case "2":
			break;
		case "3":
			break;
		case "4":
			$url = array(
				"服务报告" => "services",
				"工作报告" => "report",
				"运维文库" => "wiki",
				"修改密码" => "chgpasswd"
			);
			break;
		case "5":
			break;
	}
	if (!empty($_GET['a']) && !in_array($_GET['a'],$url)) {
		header('status: 404');
		exit;
	} else {
		return $url;
	}
}


/**
 * 4.1	管理端 -> 访问权限检测
 *
 * param $val -> 用户所属部门
 */
function access_policy() {
	$policies = explode('|' , $_SESSION['policy']);
	foreach ($policies as $value) {
		switch ($value) {
			case "MGR":
				$mgr_auth = "MGR";
				return $mgr_auth;
				break;
			case "TMGR":
				if ($_SESSION['tmpmodule'] != "") {
					$mgr_auth = "TMGR";
				}
				break;
			default:
				break;
		}
	}
	return $mgr_auth;
}

/**
 * 4.2	管理端 -> 模块权限检测
 *
 * param $val -> 部门代码
 */
function module_mgrcheck($val) {
	if (access_policy() == "MGR") {
		switch ($val) {
			case "0":
				$url = 1;
				break;
			case "1":
				break;
			case "2":
				break;
			case "3":
				break;
			case "4":
				$url = array(
					"账号管理" => "staff",
					"客户管理" => "customer",
					"设备管理" => "device",
					"备件管理" => "sparepart"
				);
				break;
			case "5":
				break;
		}
	} else {
		$url = array();
	}
	if (isset($_SESSION['tmpmodule'])){
		$urltmp = array("临时管理" => "tmp");
		$url = array_merge($url,$urltmp);
	}
	if (!empty($_GET['a']) && !in_array($_GET['a'],$url) && $val != "0") {
		header('status: 404');
	} else {
		return $url;
	}
}

?>