<?php
require_once("global.inc.php");
$code = string_filter($_GET['code']);
if ($code != "") {
	$APP_sql = new APP_SQL();
	$App_validinfo = $APP_sql -> getTableAllWhere("view_unLoginAuth","authorizer",$code);
	$id = $App_validinfo['id'];
	if ($_GET['p'] == "check" && $id != "") {
		$account = numeric_filter($_POST['account']);
		$APP_sql -> updateAccountCheck($id,$account);
		$affected = $APP_sql -> affected();
		$APP_sql -> close();
		if ($affected == 1) {
			$result = array(
				"code" => 1,
				"message" => "当前账号可用"
			);
			header('Content-Type: application/json');
			echo json_encode($result);
			exit;
		} else {
			$result = array(
				"code" => 0,
				"message" => "当前账号不可用"
			);
			header('Content-Type: application/json');
			echo json_encode($result);
			exit;
		}
	} else if ($_GET['p'] == "regist" && $id != "") {
		$account = numeric_filter($_POST['account']);
		$App_registStaff = $APP_sql -> getTableAllWhere("s_staff","id",$id);
		if ($App_registStaff['account'] == $account) {
			$passwd = sha1($_POST['passwd']);
			$App_staffResult = $APP_sql -> registStaff($id, $account, $passwd);
			$APP_sql -> close();
			if ($App_staffResult) {
				$result = array(
					"code" => 1,
					"message" => "注册成功，请<strong><a href='./'>登录</a></strong>"
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			} else {
				$result = array(
					"code" => 0,
					"message" => "注册失败"
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			}
		}
	} else {
		$name = $App_validinfo['name'];
		$gender = gender_converter($App_validinfo['gender']);
		$job = job_converter($App_validinfo['department'],$App_validinfo['position']);
		$tel = $App_validinfo['tel'];
		$APP_sql -> close();
	}
	if ($id == "") {
		header("Status: 404");
		exit;
	} else {
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
		<div id="APP_top_user"></div>
		<!-- 用户区结束 -->
		<!-- 导航区开始 -->
		<nav id="APP_top_nav">
			<ul>
				<li><a>欢迎使用本系统注册您的个人账户,确认信息无误后，请输入您的个性化账号进行注册，谢谢。</a></li>
			</ul>
		</nav>
		<!-- 导航区结束 -->
	</header>
	<!-- 顶栏结束 -->
	<!-- 主容器开始 -->
	<div id="APP">
		<!-- 内容区开始 -->
		<div id="APP_main">
				<table class="datatable">
					<tr>
						<td>姓名:</td>
						<td><?php echo $name;?></td>
						<td><input type="text" size="10" maxlength="8" name="account" id="APP_valid_account" placeholder="账号:8位数字" /></td>
						<td id="APP_account_status"></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>性别:</td>
						<td><?php echo $gender;?></td>
						<td><input type="password" size="10" name="passwd" id="APP_valid_pass" placeholder="请输入密码" /></td>
						<td class="APP_passwd_status"></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>职务:</td>
						<td><?php echo $job;?></td>
						<td><input type="password" size="10" name="passwd2" id="APP_valid_pass2" placeholder="再输一次" /></td>
						<td class="APP_passwd_status"></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>电话:</td>
						<td><?php echo $tel;?></td>
						<td><input type="button" id="APP_valid_regist" value="注册账号" /></td>
						<td id="APP_regist_status"></td>
						<td></td>
						<td></td>
					</tr>
				</table>
<?php
		APP_html_footer();
	}
} else {
	header("Status: 404");
	exit;
}
?>