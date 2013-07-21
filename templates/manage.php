<?php

/**
 * 文件不允许访问
 *
 */
if (!defined('__ROOT__')) {
	header("Status: 404");
	exit;
}

/**
 * 输出页面头部
 *
 */
function APP_html_header() {
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Language" content="zh-cn" />
	<meta http-equiv="Content-Type" content="text/html" />
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta name="copyright" content="Single Corner" /> 
	<meta name="description" content="运维管理系统" />
	<title>运维管理系统</title>
	<link rel="stylesheet" href="./css/css.css" />
	<script src="./js/jquery-1.7.2.min.js"></script>
	<script src="./js/app.js"></script>
</head>
<body>
	<!-- 顶栏开始 -->
	<header id="APP_top">
		<!-- LOGO开始 -->
		<div id="APP_top_logo">
			<a href="/"><img src="./images/logo.png" /></a>
		</div>
		<!-- LOGO结束 -->
		<!-- 用户区开始 -->
		<div id="APP_top_user"><?php echo $_SESSION['Login_jobtitle']." ".$_SESSION["Login_name"]; ?> 正在管理本系统 <a href="../">离开后台</a></div>
		<!-- 用户区结束 -->
		<!-- 导航区开始 -->
		<nav id="APP_top_nav">
			<ul>
				<li><a href="./admin.php">公告发布</a></li>
				<li><a href="./admin.php?a=member">成员管理</a></li>
			</ul>
		</nav>
		<!-- 导航区结束 -->
	</header>
	<!-- 顶栏结束 -->
	<!-- 主容器开始 -->
	<div id="AM">
		<!-- 内容区开始 -->
		<div id="APP_main">
<?php
}


/**
 * 输出页脚信息与版权信息
 *
 */
function APP_html_footer() {
?>
		</div>
		<!-- 内容区结束 -->
	</div>
	<!-- 主容器结束 -->
	<!-- 页脚开始 -->
	<footer id="APP_foot">
		<div id="APP_foot_copyright">
			Copyright © 2013 - 2015 SingleCorner<br />
			<a  href="https://github.com/SingleCorner/OMM" target="_blank">https://github.com/SingleCorner/OMM</a>
		</div>
		<div id="APP_foot_license">
			<div>SingleCorner 版权所有</div>
			<div>授权 Leyoung 使用</div>
		</div>
	</footer>
	<!-- 页脚结束 -->
</body>
</html>
<?php
}


/**
 * 页首信息
 *
 */
 function APP_mgr_main() {
	 if ($_GET['a'] == "") {
?>
			<div>欢迎使用Leyoung运维管理系统</div>
<?php
	}
 }
?>