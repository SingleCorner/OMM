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
 * 检测登出操作
 *
 */
if (isset($_GET["logout"]) && $_GET["logout"] == md5($_SESSION["timestamp"])) {
	session_destroy();
	header('Location: /');
	exit;
}


/**
 * 检测验证码状态
 *
 */
if (isset($_SESSION["AuthToken"])) {
	if (sha1($_SERVER["REMOTE_ADDR"]) != $_SESSION["AuthToken"]) {
		session_regenerate_id();	//遇到伪造session登陆的，先改变其PHPSESSID，
		session_destroy();			//再将所有值清空.
		echo "<script>alert('Warning: 在线状态异常，请重启浏览器。');window.open('','_self');window.close();</script>";
		exit;
	}
}


/**
 * 检测代理状态
 *
 */
if (__IP_ADDR__ == "") {
	echo "<script>alert('Warning: Not allow access via proxy！');window.open('','_self');window.close();</script>";
	exit;
} else {
	if (!isset($_SESSION["timestamp"])) {
		$_SESSION["timestamp"] = time();//对于每次会话，生成新的时间戳
	}
}


/**
 * 检测超时状态
 *
 */
if (isset($_SESSION["timeout_check"])) {
	$time_now = time();
	if ($time_now - $_SESSION["timeout_check"] > __SYS_TIMEOUT__) {
		session_destroy();
		header('Location: /?timeout=1');
		exit;
	} else {
		$_SESSION["timeout_check"] = $time_now;
	}
}
?>