<?php
require_once("global.inc.php");

/* 初始登录验证 */
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
				$App_passwd = sha1($App_passwd);
				$App_auth_passwd = $App_auth['passwd'];
			}
			if ($App_auth_passwd == $App_passwd) {
				$_SESSION['policy'] = $App_auth['authority'];
				$_SESSION['tmpmodule'] = $App_auth['tmpmodule'];
				$App_info = $App_sql -> getTableAllWhere("s_staff","account",$App_user);
				$_SESSION['AuthToken'] = sha1($_SERVER["REMOTE_ADDR"]);
				$_SESSION['timeout_check'] = time();
				$_SESSION['Login_account'] = $App_info['account'];
				$_SESSION['Login_name'] = $App_info['name'];
				$_SESSION['Login_section'] = $App_info['department'];
				$_SESSION['Login_jobtitle'] = job_converter($App_info['department'],$App_info['position']);
				$App_sql -> close();
				$result = array(
					"code" => 0,
					"message" => "Login success ,but the browser do not support JavaScript.We're sorry to Pls you to PRESS F5."
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			} else {
				$result = array(
					"code" => -1,
					"message" => 'OMM验证失败：账户尚未激活或已过期'
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			}
		}
	}
} else {
	if (isset($_SESSION['AuthToken']) && $_SESSION['AuthToken'] != ""){
		if (!empty($_GET['p'])) {
			include("./data_proc.php");
		}
		APP_html_header();
		APP_html_module();
		APP_html_footer();
	} else {
		APP_full_login();
	}
}

?>