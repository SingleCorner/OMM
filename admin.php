<?php
require_once("global.inc.php");

if (!isset($_SESSION['AuthToken']) || empty($_SESSION['AuthToken'])){
	header("http/1.1 403 Forbidden");
	header("Status: 403");
	exit;
}
//检测权限值通过后加载页面
if (access_policy()) {
	if (!empty($_GET['p'])) {
		if (isset($_GET['a']) && module_mgrcheck($_SESSION['Login_section'])){
			include("./data_proc_admin.php");
		} else {
			exit;
		}
	} else {
		APP_html_header();
		APP_mgr_main();
		APP_html_footer();
	}
} else {
	header("Status: 403");
	exit;
}
?>