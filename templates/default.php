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
 * 输出登录界面
 *
 */
function APP_full_login($APP_login_error = "") {
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Language" content="zh-cn" />
	<meta http-equiv="Content-Type" content="text/html" />
	<meta http-equiv="X-UA-Compatible" content="IE=10" />
	<meta name="copyright" content="Single Corner" /> 
	<meta name="description" content="运维管理系统" />
	<title>运维管理系统</title>
	<link rel="stylesheet" href="./css/login.css" />
	<script src="./js/jquery-1.7.2.min.js"></script>
	<script src="./js/jquery.sha1.js"></script>
	<script src="./js/login.js"></script>
</head>
<body>
<?php
	if (preg_match('/MSIE (\d+\.\d+)/i', $_SERVER['HTTP_USER_AGENT'], $msie) != 0 && $msie[1] < 9) {
?>
	<div id="MSIE_Warning">
		<strong>检测到您正在使用低于9.0版本的IE浏览器访问本页面！</strong>
		<p>为了给您最佳的使用体验，请<a href="http://ie.microsoft.com/" target="_blank">升级IE</a>至最新版本或使用<a href="http://chrome.google.com/" target="_blank">google浏览器</a>（推荐）。</p>
	</div>
<?php
	}
?>
	<div id="APP_login">
		<form id="APP_login_form" action="?a=login" method="post">
			<input type="hidden" id="APP_login_timestamp" value=<?php echo $_SESSION["timestamp"];?> />
			<ul>
				<li>
					<label for="APP_login_user">数字账号</label>
					<input type="text" name="username" id="APP_login_user" maxlength="8" />
				</li>
				<li>
					<label for="APP_login_pswd">密码</label>
					<input type="password" name="password" id="APP_login_pswd" autocomplete="off" />
				</li>
				<li id="APP_login_buttons">
					<input type="submit" value="登录" id="APP_login_submit" title="登录" />
				</li>
			</ul>
		</form>
		<div id="APP_login_status"><?php echo $APP_login_error; ?></div>
	</div>
	<footer>Copyright © 2013 Single Corner</footer>
</body>
</html>
<?php
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
	<script src="./js/jquery.sha1.js"></script>
	<script src="./js/app.js"></script>
</head>
<body scroll=auto>
	<!-- 顶栏开始 -->
	<header id="APP_top">
		<!-- LOGO开始 -->
		<div id="APP_top_logo">
			<a href="/"><img src="./images/logo.png" /></a>
		</div>
		<!-- LOGO结束 -->
		<!-- 用户区开始 -->
		<div id="APP_top_user"><?php echo $_SESSION['Login_jobtitle']." ".$_SESSION["Login_name"]; ?> 正在使用本系统 <a href="./?logout=<?php echo md5($_SESSION["timestamp"]); ?>">安全退出</a></div>
		<!-- 用户区结束 -->
		<!-- 导航区开始 -->
		<nav id="APP_top_nav">
			<ul>
				<li><a href="./">首页</a></li>
				<li><a href="./?a=maintenance">运维管理</a></li>
				<li><a href="./?a=report">工作报告</a></li>
				<li><a href="./?a=knowledge">知识库</a></li>
				<li><a href="./?a=chgpasswd">修改密码</a></li>
<?php if (check_policy()){ ?>
				<li><a href="./admin.php">后台管理</a></li>
<?php } ?>
			</ul>
		</nav>
		<!-- 导航区结束 -->
	</header>
	<!-- 顶栏结束 -->
	<!-- 主容器开始 -->
	<div id="APP">
		<!-- 内容区开始 -->
		<div id="APP_main">
<?php
}
/**
 * 输出页脚信息与版权信息
 *
 */
function APP_html_footer(){
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
			<div>授权 <a href="http://www.leyoung.com.cn" target="_blank">Leyoung</a> 使用</div>
		</div>
	</footer>
	<!-- 页脚结束 -->
</body>
</html>
<?php
}

/**
 * 操作区模块
 * 功能索引
 1.首页
 2.运维管理
 3.工作报告
 4.知识库
 5.更改密码
 *
 */
function APP_html_module(){
	 switch ($_GET['a']) {
		/**
		 * 功能 - > 1.首页
		 *
		 */
		 case "":
?>
	<div id="APP_chgpass">首页正在开发中，敬请期待</div>
<?php
			 break;

		
		/**
		 * 功能 - > 2.运维管理
		 *
		 */
		 case "maintenance":
?>
	<div id="APP_chgpass">运维管理模块正在开发中，敬请期待</div>
<?php
			 break;

		/**
		 * 功能 - > 3.工作报告
		 *
		 */
		 case "report":
?>
	<div id="APP_chgpass">工作报告模块正在开发中，敬请期待</div>
<?php
			 break;


		/**
		 * 功能 - > 4.知识库
		 *
		 */
		 case "knowledge":
?>
	<div id="APP_chgpass">知识库模块正在开发中，敬请期待</div>
<?php
			 break;


		/**
		 * 功能 - > 5.更改密码
		 *
		 */
		 case "chgpasswd":
?>
	<div id="APP_chgpass">
		<form id="APP_chgpass_form" action="./?a=chgpasswd&p=TRUE" method="post">
			<ul>
				<li>
					<p>
						<label>新密码</label>
						<input type="password" name="password" id="APP_new_pswda" placeholder="请输入密码" autocomplete="off" />
					</p>
				</li>
				<li>
					<p>
						<label>确认密码</label>
						<input type="password" name="password2" id="APP_new_pswdb" placeholder="请再输入一次" autocomplete="off" />
					</p>
				</li>
				<li>
					<p>
						<input type="submit" value="确认修改" id="APP_chgpass_submit" title="确认更改密码" />
					</p>
				</li>
			</ul>
		</form>
		<div id="APP_chgpass_status"></div>
	</div>
<?php
			 break;

		
		/**
		 * 功能 - > 0.其他模块
		 *
		 */
		 default:
?>
	<div id="APP_chgpass">其余模块正在开发中，敬请期待</div>
<?php
			 break;
	}
}
?>