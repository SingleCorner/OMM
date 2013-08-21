<?php
require_once("global.inc.php");

if (!isset($_SESSION['AuthToken']) || empty($_SESSION['AuthToken'])){
	header("http/1.1 403 Forbidden");
	header("Status: 403");
	exit;
}
//检测权限值通过后加载页面
if (false === access_policy()) {
	header("Status: 403");
	exit;
} else {
	if (!empty($_GET['p'])) {
		if (($_GET['a'])){
			include("./data_proc_admin.php");
		} else {
			exit;
		}
	}
		APP_html_header();
		APP_mgr_main();
		APP_html_footer();
}
?>