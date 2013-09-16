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
			<?php 
			$module = module_mgrcheck($_SESSION['Login_section']);
			foreach ($module as $key => $value) {
			?>
				<li><a href="./admin.php?a=<?php echo $value;?>"><?php echo $key;?></a></li>
			<?php 
			}
			?>
			

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
	if ($_GET['a']=="") {
	} else {
		switch ($_GET['a']) {
			case "bulletin":
?>
<?php
				break;
			case "staff":
?>
			<div class="title_container">
				<span class="title_more">
					<form id="APP_queryStaff_form" action="?a=&p=query" method="post">
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
				<div class="title_container"><h1>新账号</h1></div><br />
				<form id="APP_newstaff_form" action="?a=staff&p=add" method="post">
					姓名<input type="text" name="name" size="5" id="APP_newStaff_name" />
					性别
					<select name="gender" id="APP_newStaff_gender">
						<option value="1" selected>男</option>
						<option value="0" >女</option>
					</select>
					部门
					<select size="1" name="department" id="APP_newStaff_department">
						<option value="4" >技术部</option>
					</select>
					职位
					<select name="position" id="APP_newStaff_position">
						<option value="2" selected>系统工程师</option>
						<option value="3" >PC工程师</option>
						<option value="4" >数据库工程师</option>
					</select>
					电话<input type="text" name="tel" size="11" maxlength="11" id="APP_newStaff_tel" />
					email<input type="text" name="mail" size="11" id="APP_newStaff_mail" />
					<input type="submit" value="生成账号" />
					<span id="APP_newStaff_status"></span>
				</form>
			</div>
			<div id="APP_verifyStaff"> 
			</div>
			<div id="APP_listStaff">
				<div class="title_container"><h1>在库账号</h1></div><br />
				<table class="datatable">
					<tr>
						<th width=10%>姓名</th>
						<th width=17%>部门&职位</th>
						<th width=15%>电话</th>
						<th width=25%>email</th>
						<th width=10%>状态</th>
						<th></th>
					</tr>
				<?php 
				$APP_sql = new APP_SQL();
				$App_listStaff = $APP_sql -> getStaffList();
				$APP_sql -> close();
				while ($App_listStaff_query = $APP_sql -> fetch_assoc($App_listStaff)) {
					$id = $App_listStaff_query['id'];
					$name = $App_listStaff_query['name'];
					$job = job_converter($App_listStaff_query['department'],$App_listStaff_query['position']);
					$tel = $App_listStaff_query['tel'];
					$mail = $App_listStaff_query['mail'];
					$status = status_converter($App_listStaff_query['status']);
				?>
					<tr>
						<td><?php echo $name;?></td>
						<td><?php echo $job;?></td>
						<td><?php echo $tel;?></td>
						<td><?php echo $mail;?></td>
						<td><?php echo $status;?></td>
						<td>
							<button>账号赋权</button>
							<button>冻结账号</button>
						</td>
					</tr>
				<?php
				} 
				?>
				</table>
			</div>
<?php
				break;
			case "customer":
?>
			<div class="title_container">
				<span class="title_more">
					<form id="APP_queryStaff_form" action="?a=&p=query" method="post">
						<input type="text" size="10" name="name" id="APP_queryCustomer_name" placeholder="公司名称" />
						<input type="button" value="查询" />
					</form>
				</span>
				<h1>
					<button onclick="load_newCustomer()">建立新客户</button>
				</h1>
			</div>
			<div id="APP_newCustomer">
				<div class="title_container"><h1>客户资料</h1></div><br />
				<form id="APP_newcustomer_form" action="?a=customer&p=add" method="post">
					客户名称<input type="text" name="name" size="20" id="APP_newCustomer_name" />
					联系人<input type="text" name="contact" size="10" maxlength="11" id="APP_newCustomer_contact" />
					联系电话<input type="text" name="tel" size="11" id="APP_newCustomer_tel" />
					客户地址<input type="text" name="addr" size="11" id="APP_newCustomer_addr" />
					<input type="submit" value="添加客户" />
					<span id="APP_new_status"></span>
				</form>
			</div>
			<div id="APP_listCustomer">
				<div class="title_container"><h1>现有客户</h1></div><br />
				<table class="datatable">
					<tr>
						<th width=10%>客户编号</th>
						<th width=25%>客户名称</th>
						<th width=10%>联系人</th>
						<th width=15%>联系电话</th>
						<th width=15%>客户地址</th>
						<th></th>
					</tr>
				<?php 
				$APP_sql = new APP_SQL();
				$App_listCustomer = $APP_sql -> getCustomerList();
				$APP_sql -> close();
				while ($App_listCustomer_query = $APP_sql -> fetch_assoc($App_listCustomer)) {
					$id = $App_listCustomer_query['id'];
					$name = $App_listCustomer_query['name'];
					$contacter = $App_listCustomer_query['contact'];
					$tel = $App_listCustomer_query['tel'];
					$address = $App_listCustomer_query['address'];
					$status = $App_listCustomer_query['status'];
					if ($_SESSION['Login_section'] == 0 || $status % 2 == 1) {
				?>
					<tr class="<?php echo "APP_customer_".$id; ?>">
						<td onclick=""><?php echo $id;?></td>
						<td><?php echo $name;?></td>
						<td><?php echo $contacter;?></td>
						<td><?php echo $tel;?></td>
						<td><?php echo $address;?></td>
						<td>
							<button onclick="">客户资料修改</button>
						<?php if ($status % 2 == 1) {?>
							<button onclick="unlinkCustomer(<?php echo $id;?>)">断开合作</button>
						<?php } else {?>
							<button onclick="unlinkCustomer(<?php echo $id;?>)">重新合作</button>
						<?php }?>
						</td>
					</tr>
				<?php
					}
				} 
				?>
				</table>
			</div>
<?php
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
}
?>