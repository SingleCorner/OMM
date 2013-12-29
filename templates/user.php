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
	<script src="./js/jquery.print.js"></script>
	<script src="./js/app.js"></script>
	<script src="/CKE/ckeditor.js"></script>
	<script src="/CKE/adapters/jquery.js"></script>
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
					<!--<canvas id="canvas" width="150" height="150"></canvas>-->
					<div>当前日期 <?php echo date("Y-m-d");?></div>
					<script>clockinit()</script>
<?php
if ($App_worktime['recordtime'] != "") {
	$_SESSION['index_recordtime'] = $App_worktime['recordtime'];
	$lefttime = $App_worktime['onwork'] + $App_worktime['overwork'] - $App_worktime['rest'];
	$APP_sql = new APP_SQL();
	$App_workrecord = $APP_sql -> getWorkrecord();
	if ($App_workrecord === FALSE) {
		$date = date("Y-m-01");
		$APP_sql -> insertWorkrecord($date);
		$APP_sql -> close();
		header('Location: /');
	} else {
		$status = $App_workrecord['checkstatus'];
		$date = $App_workrecord['date'];
		$_SESSION['workrecord_date'] = $date;
		$weekno = date("N",strtotime($date));
		if ($status == 4) {
			if ($date != date("Y-m-d")) {
				$date = date("Y-m-d", strtotime($date."+ 1 day"));
				$APP_sql -> insertWorkrecord($date);
				header('Location: /');
			} else {
?>
					<div id="APP_signal_opbtn">
						<div><?php echo "今日已记录";?></div>
					</div>
<?php
		}
		$APP_sql -> close();
	} else {
		$APP_sql -> close();
		if (strtotime($date) < strtotime(date("Y-m-d"))) {
			$tips = "历史补录";
		} else {
			$tips = "今日记录";
		}
		$tips = $tips." ".$date;
		switch ($status) {
			case "0":
?>
					<div id="APP_signal_opbtn">
						<div><?php echo $tips;?></div>
						<div>
							是否公休假期
					<?php
						if ($weekno == 6 || $weekno == 7) {
					?>
							<input type="radio" value="1" name="signal_weekend" checked />是
							<input type="radio" value="0" name="signal_weekend" />否
					<?php
						} else {
					?>
							<input type="radio" value="1" name="signal_weekend" />是
							<input type="radio" value="0" name="signal_weekend" checked />否
					<?php
						}
					?>
						</div>
						<span class="signal_btn"><button onclick=check_op("in",1)>公司上班</button></span>
						<span class="signal_btn"><button onclick=check_op("in",2)>中心值班</button></span>
						<span class="signal_btn"><button onclick=check_op("in",3)>休息/调休</button></span>
					</div>
					<?php
									break;
								case "1":
					?>
					<div id="APP_signal_opbtn">
						<div><?php echo $tips;?></div>
						<span class="signal_btn"><button onclick=check_op("out",1)>公司下班</button></span>
						<span class="signal_btn"><button onclick=check_op("transfer",2)>中心加班</button></span>
					</div>
					<?php
									break;
								case "2":
					?>
					<div id="APP_signal_opbtn">
						<div><?php echo $tips;?></div>
						<select id="APP_signal_outtimeh">
							<option value="-1">正常下班</option>
							<option value="0">18点</option>
							<option value="1">19点</option>
							<option value="2">20点</option>
							<option value="3">21点</option>
							<option value="4">22点</option>
							<option value="5">23点</option>
							<option value="8" selected>00点</option>
							<option value="9">01点</option>
							<option value="10">02点</option>
							<option value="11">03点</option>
							<option value="12">04点</option>
							<option value="13">05点</option>
							<option value="14">06点</option>
							<option value="15">07点</option>
							<option value="16">08点</option>
							<option value="17">09点</option>
						</select>
						<select id="APP_signal_outtimem">
							<option value="0">00分</option>
							<option value="0.5">30分</option>
						</select>
						<span class="signal_btn"><button onclick=check_op("out",2)>中心下班</button></span>
					</div>
					<?php
									break;
								case "3":
					?>
					<div id="APP_signal_opbtn">
						<div><?php echo $tips;?></div>
						<span class="signal_btn"><button onclick=check_op("out",3)>无加班</button></span>
						<span class="signal_btn"><button onclick=check_op("inn",2)>中心加班</button></span>
					</div>
					<?php
									break;
								default:
									break;
							}
						}
					}

				?>
					<div id="APP_signal_timer">
						<table class="datatable">
							<tr>
								<td width=40%>中心值班时间</td>
								<td width=25%><?php echo $App_worktime['onwork'];?></td>
								<td width=35%>小时</td>
							</tr>
							<tr>
								<td>中心加班时间</td>
								<td><?php echo $App_worktime['overwork'];?></td>
								<td>小时</td>
							</tr>
							<tr>
								<td>已用调休时间</td>
								<td><?php echo $App_worktime['rest'];?></td>
								<td>小时</td>
							</tr>
							<tr>
								<td>当前剩余时间</td>
								<td><?php echo $lefttime;?></td>
								<td>小时</td>
							</tr>
						</table>
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
				<!--2个月内签到信息-->
				<div id="APP_signal_info">
					<table class="datatable">
						<tr>
							<th>日期</th>
							<th>公司上班</th>
							<th>公司下班</th>
							<th>中心上班</th>
							<th>中心下班</th>
						</tr>
<?php
				$APP_sql = new APP_SQL();
				$APP_sqlquery = $APP_sql -> getWorkrecordList();
				$APP_sql -> close();
				while ($APP_result = $APP_sqlquery -> fetch_assoc()){
					if ($APP_result['comcheckin'] == "00:00:00") {
   						$APP_result['comcheckin'] = $APP_result['comcheckout'] = "调休";
					}
					if ($APP_result['checkstatus'] == 4 && empty($APP_result['comcheckin']) && empty($APP_result['cencheckin'])) {
   						$APP_result['comcheckin'] = $APP_result['comcheckout'] = $APP_result['cencheckin'] = $APP_result['cencheckout'] = "---";
					}
?>
						<tr>
							<td><?php echo $APP_result['date']." ".date("D",strtotime($APP_result['date']));?></td>
							<td><?php echo $APP_result['comcheckin'];?></td>
							<td><?php echo $APP_result['comcheckout'];?></td>
							<td><?php echo $APP_result['cencheckin'];?></td>
							<td><?php echo $APP_result['cencheckout'];?></td>
						</tr>
<?php
	}
?>

					</table>
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
			<div class="title_container">
				<span class="title_more">
					<form id="APP_querySrvs_form" action="?a=services&p=query" method="post">
						<input type="text" size="10" name="id" id="APP_querySrvs_id" placeholder="服务编号" />
						<input type="text" size="10" name="name" id="APP_querySrvs_date" placeholder="日期" />
						<input type="submit" value="查询" />
					</form>
				</span>
				<h1>
					<button onclick="load_newSrvs()">填写服务报告单</button>
				</h1>
			</div>
			<div id="APP_newSrvs">
				<form id="APP_newSrvs_form" action="?a=services&p=add" method="post">
					<div class="title_container"><img src="<?php echo __COM_LOGO__;?>" height="30px" /><span class="title_more">LY-QR-09-13</span></div>
					<h2>客  户 服 务 报 告 单</h2>
					<div>客户信息：
						<select id="APP_newSrvs_customer" name="customer">
							<option value="0">请选择</option>
<?php
					$APP_sql = new APP_SQL();
					$sql_customer = "SELECT * from `s_customer` WHERE `display` = '1';";
					$query = $APP_sql -> userDefine($sql_customer);
					$APP_sql -> close();
					while ($APP_result = $query -> fetch_assoc()) {
						$id = $APP_result['id'];
						$name = $APP_result['nickname'];
?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
<?php
					}

?>
						</select>
					</div>
					<div>
						<table class="formtable">
							<tr>
								<td width=15%>客户名称: </td>
								<td id="APP_newSrvs_cname" width=40%></td>
								<td>联 系 人：</td>
								<td id="APP_newSrvs_ccontact"></td>
							</tr>
							<tr>
								<td>地 址：</td>
								<td id="APP_newSrvs_caddr"></td>
								<td>电 话：</td>
								<td id="APP_newSrvs_ctel"></td>
							</tr>
						</table>
					</div>
					<div>客户报修/需求：
						<select id="APP_newSrvs_fixid" name="fixid">
							<option value="0">请选择</option>
<?php
					$APP_sql = new APP_SQL();
					$sql_wiki = "SELECT * from `s_wiki` WHERE `type` = '4' AND `subtype` = '1';";
					$query = $APP_sql -> userDefine($sql_wiki);
					$APP_sql -> close();
					while ($APP_result = $query -> fetch_assoc()) {
						$id = $APP_result['id'];
						$name = $APP_result['headline'];
?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
<?php
					}

?>
						</select>
						<input id="APP_newSrvs_requirement" name="requirement" type="text" placeholder="请先选择需求后更改" />
					</div>
					<div>服务类型：</div>
					<div>
						<table class="formtable">
							<tr>
								<td><label><input name="stype" type="radio" value="1" />保内服务</label></td>
								<td><label><input name="stype" type="radio" value="2" />利银MA</label></td>
								<td><label><input name="stype" type="radio" value="3" />厂商MA</label></td>
								<td><label><input name="stype" type="radio" value="4" />无备件MA</label></td>
								<td><label><input name="stype" type="radio" value="5" />保外服务</label></td>
							</tr>
							<tr>
								<td><label><input name="mtype" type="radio" value="1" />生产系统维护</label></td>
								<td><label><input name="mtype" type="radio" value="2" />备份系统维护</label></td>
								<td><label><input name="mtype" type="radio" value="3" />测试系统维护</label></td>
								<td><label><input name="mtype" type="radio" value="4" />备机维护</label></td>
							</tr>
						</table>
					</div>
					<div>服务时间：</div>
					<div>
						<table class="formtable">
							<tr>
								<td>服务开始时间：<input id="APP_newSrvs_start" name="start" type="text" placeholder="<?php echo date("Y-m-d\ H:i:s");?>" /></td>
								<td>服务结束时间：<input id="APP_newSrvs_end" name="end" type="text" placeholder="<?php echo date("Y-m-d\ H:i:s");?>" /></td>
							</tr>
						</table>
					</div>
					<div>服务人员：</div>
					<div>
						<table class="formtable">
							<tr>
								<td>主要实施人员：<input id="APP_newSrvs_main" name="main" type="text" /></td>
								<td>协助实施人员：<input id="APP_newSrvs_sub" name="sub" type="text" /></td>
							</tr>
						</table>
					</div>
					<div>陪同人员：</div>
					<div>
						<table class="formtable">
							<tr>
								<td>客户陪同人 ： <input id="APP_newSrvs_accompany" name="accompany" type="text" /></td>
							</tr>
						</table>
					</div>
					<div>系统描述：</div>
					<div><textarea id="APP_newSrvs_sysdescr" name="sysdescr" class="ckeditor"></textarea></div>
					<div>具体工作内容：</div>
					<div><textarea id="APP_newSrvs_workdescr" name="workdescr" class="ckeditor"></textarea></div>
					<div>操作步骤：</div>
					<div id="APP_newSrvs_opmethod"></div>
<!--					<div>客户评价：</div>
					<div>意见与意见：</div>
					<div>
						<table class="formtable">
							<tr>
								<td>客户签名：</td>
								<td></td>
								<td></td>
								<td>工程师签名：</td>
								<td></td>
							</tr>
							<tr>
								<td>日期：</td>
								<td></td>
								<td></td>
								<td>日期：</td>
								<td></td>
							</tr>
						</table>
					</div>
-->					<center>上海利银科技有限公司</center>
					<div><input class="red" type="submit" value="提交服务报告单" /></div>
				</form>
			</div>
			<div id="APP_listSrvs">
				<div class="title_container"><h1>服务报告单列表</h1></div><br />
				<table class="datatable">
					<tr>
						<th width=10%>ID</th>
						<th width=25%>服务需求</th>
						<th>服务时间</th>
						<th width=10%>实施工程师</th>
						<th width=15%>服务单填写人</th>
						<th width=15%>操作</th>
					</tr>
<?php
			$APP_sql = new APP_SQL();
			if (isset($_GET['page']) && $_GET['page'] >= 1) {
				$curpage = floor($_GET['page']); 
			} else {
				$curpage = 1; 
			}
			if (isset($_GET['record']) && $_GET['record'] >= 1) {
				$records = $_GET['record'];
			} else {
				$records = 15;
			}
			$start = ($curpage - 1) * $records;
			$query = $APP_sql -> getServicesList($start,$records);
			$APP_countSrvs = $APP_sql ->getServicesList();
			$App_result_rows = $APP_countSrvs -> num_rows;
			$APP_sql -> close();
			if (($App_result_rows / $records) > floor($App_result_rows / $records) || $App_result_rows == 0) {
				$pages = floor($App_result_rows / $records) + 1;
			} else {
				$pages = $App_result_rows / $records;
			}
			$url_fp = "?a=services";
			if ($curpage < $pages) {
				$prepage = $curpage - 1;
				$nxpage = $curpage + 1;
			} else {
				$prepage = $curpage - 1;
				$nxpage = $pages;
			}
			if ($curpage == 1) {
				$prepage = 1;
			}
			$url_pp = "?a=services&record=".$records."&page=".$prepage;
			$url_np = "?a=services&record=".$records."&page=".$nxpage;
			$url_lp = "?a=services&record=".$records."&page=".$pages;
			while ($APP_result = $query -> fetch_assoc()) {
				$id = $APP_result['id'];
				$need = $APP_result['headline'];
				$start = date("Y-m-d",strtotime($APP_result['stime']));
				$end = date("Y-m-d",strtotime($APP_result['etime']));
				$engineer = $APP_result['mainmbr'];
				$writer = $APP_result['engineer'];
?>
					<tr>
						<td><?php echo $id;?></td>
						<td><?php echo $need;?></td>
						<td><?php echo $start." 至 ".$end;?></td>
						<td><?php echo $engineer;?></td>
						<td><?php echo $writer;?></td>
						<td><button class="query_srvs" value="<?php echo $id;?>">查看</button><button class="print_srvs" value="<?php echo $id;?>">打印</button></td>
					</tr>
<?php
			}
?>
				</table>
				<p></p>
				<center><a href="<?php echo $url_fp;?>"><<</a><a href="<?php echo $url_pp;?>"><</a><a href="<?php echo $url_np;?>">></a><a href="<?php echo $url_lp;?>">>></a></center>
				<center><?php echo $_SERVER['PHP_SCRIPT'];?></center>
			</div>
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
			<div class="title_container">
				<span class="title_more">
					<form id="APP_queryWIKI_form" action="?a=wiki&p=query" method="post">
						<input type="text" size="10" name="name" id="APP_queryWIKI_title" placeholder="wiki标题" />
						<input type="submit" value="查询" />
					</form>
				</span>
				<h1>
					<button onclick="load_newWIKI()">新增技术文档</button>
				</h1>
			</div>
			<div id="APP_newWIKI">
				<div class="title_container"><h1>新技术文档</h1></div><br />
				<form id="APP_newWIKI_form" action="?a=wiki&p=add" method="post">
					文档标题<input type="text" name="title" size="30" id="APP_newWIKI_title" />
					文档分类
					<select name="type" id="APP_newWIKI_type">
						<option value="1" selected>运维规范</option>
						<option value="0" >技术学习</option>
					</select>
					<br /><br />
					<textarea name="content" class="ckeditor" id="APP_newWIKI_content"></textarea>
					<br />
					<input type="submit" value="提交文档" />
					<input type="reset" value="清空文档" />
				</form>
			</div>
			<div id="APP_listWIKI">
				<div class="title_container"><h1>文档列表</h1></div><br />
				<table class="datatable">
					<tr>
						<th width=5%>ID</th>
						<th width=30%>文档标题</th>
						<th>文档内容</th>
						<th width=10%>贡献者</th>
					</tr>
				<?php
				$APP_sql = new APP_SQL();
				$App_listWIKI = $APP_sql -> getTableAll("s_wiki");
				$APP_sql -> close();
				while ($App_listWIKI_query = $APP_sql -> fetch_assoc($App_listWIKI)) {
					$id = $App_listWIKI_query['id'];
					$title = $App_listWIKI_query['headline'];
					$content = $App_listWIKI_query['body'];
					$owner = $App_listWIKI_query['owner'];
				?>
					<tr>
						<td><?php echo $id;?></td>
						<td><?php echo $title;?></td>
						<td><?php echo $content;?></td>
						<td><?php echo $owner;?></td>
					</tr>
				<?php
				}
				?>
				</table>
			</div>
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