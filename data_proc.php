<?php
/**
 * 前端数据处理程序
 * 功能索引
 *	1.用户更改密码
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

 switch ($_GET['a']) {
	/**
	 * 功能 - > 1
	 *
	 */
	case 'chgpasswd':
		if (isset($_POST['password']) && $_GET['p'] == "TRUE") {
			$APP_newpasswd = sha1($_POST['password']);
			$APP_sql = new APP_SQL();
			$APP_chgpass = $APP_sql -> updateLoginPasswd($APP_newpasswd,$_SESSION['Login_account']);
			$App_affected = $APP_sql -> affected();
			$APP_sql -> close();
			if ($App_affected >= 1) {
				$result = array(
					"code" => 1,
					"message" => "你已经成功修改了密码"
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			} else {
				$result = array(
					"code" => 0,
					"message" => "未变更的密码"
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			}
		}
		break;
	/**
	 * 功能 - > 2
	 *
	 */
	case 'maintanance':
		echo 123;
		break;
	/**
	 * 功能 - > 其他
	 *
	 */
	default:
		 
		break;
}
	
?>

