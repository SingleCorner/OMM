<?php
/**
 * 后端数据处理程序
 * 功能索引
 *	1.添加成员
 *	2.
 *
 */

/**
 * 文件不允许独立访问
 *
 */
if (!defined('__ROOT__')) {
	header("Status: 404");
	exit;
}


$action = explode('_',$_GET['a']);
$module_name = $action[1];

switch ($module_name) {
	/**
	 * 功能 - > 1
	 *
	 */
	case "member":
		switch($_GET['p']){
			case "add":
				$name = string_filter($_POST['name']);
				$gender = numeric_filter($_POST['gender']);
				$department = numeric_filter($_POST['department']);
				$position = numeric_filter($_POST['position']);
				$tel = numeric_filter($_POST['tel']);
//				$authorizer = substr(sha1(time()),3,7);
				if (empty($name) || empty($gender) || empty($department) || empty($position)) {
					$result = array(
						"code" => 0,
						"message" => "数据提交异常"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				} else {
					$APP_sql = new APP_SQL();
					$sql = "INSERT INTO `s_staff` (`name`,`gender`,`department`,`position`,`tel`,`status`,`regist_time`) values ('{$name}', '{$gender}', '{$department}', '{$position}', '{$tel}', '0', now());";
					$APP_sql -> userDefine($sql);
					$App_affected = $APP_sql -> affected();
					$APP_sql -> close();
				}
				if ($App_affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "新账号已生成，请去<button onclick=load_verifyStaff()>审核账号</button>"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				} else {
					$result = array(
						"code" => 0,
						"message" => "数据提交异常"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				}
				break;
		}
		break;

	/**
	 * 功能 - > 其他
	 *
	 */
	default:
		break;
}

?>