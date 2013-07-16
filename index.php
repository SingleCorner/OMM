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
			$App_username = string_filter($_POST["username"]);
			$App_passwd = $_POST["password"];

			$App_sql = new APP_SQL();
			$App_auth = $App_sql -> LoginAuth($App_username);
			if ($_POST['encrypto'] == "on"){
				$App_auth['passwd'] = sha1($App_auth['passwd'].$_SESSION["timestamp"]);
			} else {
				$App_auth['passwd'] = $App_passwd;
			}
			if ($App_auth['passwd'] == $App_passwd) {
				$App_info = $App_sql -> getTableAllWhere("s_staff","account",$App_username);
				$_SESSION['AuthToken'] = sha1($_SERVER["REMOTE_ADDR"]);
				$_SESSION['timeout_check'] = time();
				$_SESSION['Login_name'] = $App_info['name'];
				$_SESSION['Login_department'] = $App_info['department'];
				$_SESSION['Login_level'] = $App_info['level'];
				$_SESSION['Login_position'] = $App_info['position'];
				$result = array(
					"code" => 0
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			} else {
				$result = array(
					"code" => -1,
					"message" => 'Ajax:验证失败或用户尚未激活'
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