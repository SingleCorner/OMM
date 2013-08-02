<?php
require_once("global.inc.php");

//检测权限值并显示相应的页面
if (false === check_policy()) {
	header("Status: 403");
	exit;
} else {
		APP_html_header();
		APP_mgr_main();
		APP_html_footer();
}
?>