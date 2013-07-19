<?php

/**
 * 文件不允许访问
 *
 */
if ($_SERVER['REQUEST_URI'] == $_SERVER["PHP_SELF"]) {
	header("Status: 404");
	exit;
}

/**
 * 字符过滤
 *
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
 * 职位标签输出程序
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

?>