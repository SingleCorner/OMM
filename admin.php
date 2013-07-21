<?php
require_once("global.inc.php");

//检测权限值并显示相应的页面
if (check_policy($_SESSION['policy']) != "SU" && check_policy($_SESSION['policy']) != "MGR" ) {
	header("Status: 403");
	exit;
} else {
		APP_html_header();
		APP_html_footer();
}
?>