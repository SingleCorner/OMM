<?php
require_once("global.inc.php");

/* 判断登录指令 */
if (isset($_GET['a']) && $_GET['a'] == 'login'){
	if (isset($_SESSION['AuthToken']) && $_SESSION['AuthToken'] != ""){
		header('Location: /');
	} else {
		if (!$_POST["username"]) {
			header('Location: /');
		} else {
			$App_user = string_filter($_POST["username"]);
			$App_passwd = $_POST["password"];

			$App_sql = new APP_SQL();
			$App_auth = $App_sql -> LoginAuth($App_user);
			if ($_POST['encrypto'] == "on"){
				$App_auth_passwd = sha1($App_auth['passwd'].$_SESSION["timestamp"]);
			} else {
				$App_auth_passwd = $App_passwd;
			}
			if ($App_auth_passwd == $App_passwd) {
				$App_info = $App_sql -> getTableAllWhere("s_staff","account",$App_user);
				$_SESSION['AuthToken'] = sha1($_SERVER["REMOTE_ADDR"]);
				$_SESSION['timeout_check'] = time();
				$_SESSION['Login_account'] = $App_info['account'];
				$_SESSION['Login_name'] = $App_info['name'];
				$_SESSION['Login_jobtitle'] = job_converter($App_info['department'],$App_info['position']);
				$App_policy = $App_sql -> getTableAllWhere("s_authority","account",$App_user);
				$_SESSION['policy'] = $App_policy['authority'];
				$App_sql -> close();
				$result = array(
					"code" => 0
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			} else {
				$result = array(
					"code" => -1,
					"message" => 'Ajax:用户验证失败或尚未激活'
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			}
		}
	}
} else {
	if (isset($_SESSION['AuthToken']) && $_SESSION['AuthToken'] != ""){
		APP_html_header();
		APP_html_footer();
	} else {
		APP_full_login();
	}
}

?>