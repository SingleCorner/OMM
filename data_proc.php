<?php
/**
 * 前端数据处理程序
 * 功能索引
 *	1.修改密码
 *	2.服务报告单
 *	3.WIKI
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
	 * 功能 - > 修改密码
	 *
	 */
	case "chgpasswd":
		if (isset($_POST['password']) && $_GET['p'] == "TRUE") {
			$APP_newpasswd = sha1($_POST['password']);
			$APP_sql = new APP_SQL();
			$APP_sql -> updateLoginPasswd($APP_newpasswd,$_SESSION['Login_account']);
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
	case 'services':
		echo 123;
		break;
		exit;
	/**
	 * 功能 - > 3
	 *
	 */
	case '':
		switch ($_GET['p']) {
			case "recordtime":
				if (!empty($_SESSION['index_recordtime'])) {
					exit;
				}
				$onwork = $_POST['onwork'];
				$overwork = $_POST['overwork'];
				$rest = $_POST['rest'];

				//对于非法传入的值全部置0
				if (!is_numeric($onwork)) {
					$onwork = 0;
				}
				if (!is_numeric($overwork)) {
					$overwork = 0;
				}
				if (!is_numeric($rest)) {
					$rest = 0;
				}

				$APP_sql = new APP_SQL();
				$APP_sql -> updateWorktime($onwork, $overwork, $rest, $_SESSION['Login_account']);
				$App_affected = $APP_sql -> affected();
				$APP_sql -> close();
				if ($App_affected == 1) {
					$result = array (
							"code" => 1
						);
				} else {
					$result = array (
							"code" => 0
						);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
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

