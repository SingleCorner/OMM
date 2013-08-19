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
	<script src="./js/mgr.js"></script>
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
				<li><a href="./admin.php?a=T_notice">公告发布</a></li>
				<li><a href="./admin.php?a=T_member">账号管理</a></li>
				<li><a href="./admin.php?a=T_customer">客户管理</a></li>
				<li><a href="./admin.php?a=T_device">设备管理</a></li>
				<li><a href="./admin.php?a=T_sparepart">备件管理</a></li>
				<li><a href="./admin.php?a=test">测试连接</a></li>
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
 * 页首信息
 *
 */
function APP_mgr_main() {
	if (is_policy($_GET['a'])||$_GET['a'] == null) {
		if ($_GET['a']=="") {
		} else {
			$action = explode('_',$_GET['a']);
			$module_name = $action[1];
			switch ($module_name) {
				case "":
					echo "不存在的模块";
					break;
				case "notice":
					break;
				case "member":
//					$APP_sql = new APP_SQL();
//					$App_listStaff = $APP_sql -> getStaffList();
//					$App_listStaff_query = $APP_sql -> fetch_assoc($App_listStaff);
?>
			<div class="title_container">
				<span class="title_more">
					<form id="APP_queryStaff_form" action="?a=&p=query" method="post">
						<input type="text" size="10" name="account" id="APP_queryStaff_id" placeholder="编号" />
						<input type="text" size="10" name="name" id="APP_queryStaff_name" placeholder="姓名" />
						<input type="submit" value="查询" />
					</form>
				</span>
				<h1>
					<button onclick="load_newStaff()">新账户</button>
					<button onclick="load_verifyStaff()">账户审核</button>
				</h1>
			</div>
			<div id="APP_newStaff">
				<form id="APP_newstaff_form" action="?a=T_member&p=add" method="post">
					姓名<input type="text" name="name" id="APP_newStaff_name" />
					性别
					<select name="gender" id="APP_newStaff_gender">
						<option value="1" selected>男</option>
						<option value="0" >女</option>
					</select>
					部门
					<select name="department" id="APP_newStaff_department">
						<option value="4" >技术部</option>
					</select>
					职位
					<select name="position" id="APP_newStaff_position">
						<option value="2" selected>系统工程师</option>
						<option value="3" >PC工程师</option>
						<option value="4" >数据库工程师</option>
					</select>
					电话<input type="text" name="tel" size="11" maxlength="11" id="APP_newStaff_tel" />
					<input type="submit" value="生成账号" />
					<span id="APP_newStaff_status"></span>
				</form>
			</div>
			<div id="APP_verifyStaff"> 
			</div>
			<div id="APP_listStaff">
			</div>
<?php
					break;
				case "customer":
					break;
				case "device":
					break;
				case "sparepart":
					break;
				default:
					echo "不存在的模块";
					break;
			}
		}
	} else {
		echo "未授权的模块";
//		header('Location: /admin.php');
		
	}
}
?>