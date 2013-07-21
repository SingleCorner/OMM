<?php

/**
 * 配置文件不允许访问
 *
 */
if ($_SERVER['REQUEST_URI'] == "/global.inc.php") {
	header("Status: 404");
	exit;
}

session_start();
define("__ROOT__", dirname(__FILE__));//定义网站的根目录
require_once(__ROOT__.'/inc/config.inc.php');//加载主配置文件
require_once(__ROOT__.'/templates/default.php');//加载网站模块
?>