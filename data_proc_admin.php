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
				$name = $_POST['name'];
				$gender = $_POST['gender'];
				$department = $_POST['department'];
				$position = $_POST['position'];
				$tel = $_POST['tel'];
//				$APP_sql = new APP_SQL();
			if ($name != "") {
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