<?php

/**
 * 加载 -> 登录界面
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
 * 加载 -> 用户页面头部
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
			<?php 
			$module = module_usercheck($_SESSION['Login_section']);
			foreach ($module as $key => $value) {
			?>
				<li><a href="./?a=<?php echo $value;?>"><?php echo $key;?></a></li>
			<?php
			}
			if (access_policy()) { 
			?>
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
 * 操作区模块
 * 功能索引
 1.首页
 2.服务报告
 3.工作报告
 4.WIKI
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
			$APP_sql = new APP_SQL();
			$App_worktime = $APP_sql -> getTableAllWhere("s_worktime", "account", $_SESSION['Login_account']);
			$APP_sql -> close();
			if ($App_worktime['account'] != "") {			
?>
			<!-- 签到系统 -->
			<div id="APP_signal">
				<div id="APP_signal_op">
					<div>当前日期 <?php echo date("Y-m-d");?></div>
				<?php
				if ($App_worktime['recordtime'] != "") {
				?>
					<div id="APP_signal_opbtn">
						<span class="signal_btn"><button>公司签到</button></span>
						<span class="signal_btn"><button>中心签到</button></span>
						<span class="signal_btn"><button>今日调休</button></span>
					</div>
					<div>
						<div>累积加班时间</div>
						<div>累积调休时间</div>
						<div>当前剩余时间</div>

					</div>
				<?php
				} else {
				?>
					<div id="APP_signal_noinfo">
						<div>请完善加班信息</div>
						<form id="APP_signal_form" method="post" action="?a=&p=recordtime">
							<div>累计值班时间 <input id="signal_onwork" name="onwork" type="text" size="8" placeholder="例如0" /> 小时</div>
							<div>累计加班时间 <input id="signal_overwork" name="overwork" type="text" size="8" placeholder="例如8" /> 小时</div>
							<div>累计调休时间 <input id="signal_rest" name="rest" type="text" size="8" placeholder="例如0" /> 小时</div>
							<div><input type="submit" value="开始记录工作时间" /></div>
							<div>TIPS：如不确定，请仅在加班时间处填写当前剩余时间</div>
					</form>
					</div>
				<?php
				}
				?>
				</div>
				<div id="APP_signal_info">
					<div class="signal_center">加班详细信息</div>
				</div>
			</div>




<?php
			}
			 break;

		
		/**
		 * 功能 - > 2.运维管理
		 *
		 */
		 case "services":
?>
	<center>运维管理模块正在开发中，敬请期待</center>
<?php
			 break;

		/**
		 * 功能 - > 3.工作报告
		 *
		 */
		 case "report":
?>
	<center>工作报告模块正在开发中，敬请期待</center>
<?php
			 break;


		/**
		 * 功能 - > 4.知识库
		 *
		 */
		 case "wiki":
?>
	<center>知识库模块正在开发中，敬请期待</center>
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
	<center>其余模块正在开发中，敬请期待</center>
<?php
			 break;
	}
}

?>