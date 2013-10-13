<?php
/**
 * 前端数据处理程序
 * 功能索引
 *	1.修改密码
 *	2.服务报告单
 *	3.首页
 *	4.WIKI
 *
 */

/**
 * 文件不允许独立访问
 *
 */
if (!defined('__ROOT__')) {
	header("Status: 404");
	exit;
}

 switch ($_GET['a']) {
	/**
	 * 功能 - > 修改密码(start)
	 *
	 */
	case "chgpasswd":
		if (isset($_POST['password']) && $_GET['p'] == "TRUE") {
			$APP_newpasswd = sha1($_POST['password']);
			$APP_sql = new APP_SQL();
			$APP_sql -> updateLoginPasswd($APP_newpasswd,$_SESSION['Login_account']);
			$App_affected = $APP_sql -> affected();
			$APP_sql -> close();
			if ($App_affected >= 1) {
				$result = array(
					"code" => 1,
					"message" => "你已经成功修改了密码"
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			} else {
				$result = array(
					"code" => 0,
					"message" => "未变更的密码"
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			}
		}
		break;
	/**
	 * 功能 - > 修改密码(end)
	 *
	 */
	/**
	 * 功能 - > 服务报告单(start)
	 *
	 */
	case 'services':
		switch ($_GET['p']) {
			//查询客户信息
			case "query_customer":
				$id = numeric_filter($_POST['id']);
				if ($id >= 1) {
					$APP_sql = new APP_SQL();
					$query = $APP_sql -> getTableAllWhere("s_customer","id",$id);
					$APP_sql -> close();
					if ($query['status'] % 2 == 1) {
						$result = array(
							"code" => 1,
							"name" => $query['name'],
							"contact" => $query['contact'],
							"tel" => $query['tel'],
							"addr" => $query['address']
						);
					} else {
						$result = array(
							"code" => 0,
							"message" => "客户不存在或已无合作关系"
						);
					}
				} else {
					$result = array(
						"code" => 0,
						"message" => "非法输入"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
				break;
			//查询操作步骤
			case "query_opmethod":
				$id = numeric_filter($_POST['id']);
				if ($id >= 1) {
					$APP_sql = new APP_SQL();
					$query = $APP_sql -> getTableAllWhere("s_wiki","id",$id);
					$APP_sql -> close();
					if ($query['subtype'] == 1) {
						$result = array(
							"code" => 1,
							"content" => $query['body']
						);
					} else {
						$result = array(
							"code" => 0,
							"message" => "文档错误"
						);
					}
				} else {
					$result = array(
						"code" => 0,
						"message" => "非法输入"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
				break;
			case "add":
				$customer = numeric_filter($_POST['customer']);
				$fixid = numeric_filter($_POST['fixid']);
				$stype = numeric_filter($_POST['stype']);
				$mtype = numeric_filter($_POST['mtype']);
				$start = date("Y-m-d\ H:i:s",strtotime($_POST['start']));
				$end = date("Y-m-d\ H:i:s",strtotime($_POST['end']));
				$main = string_filter($_POST['main']);
				$sub = string_filter($_POST['sub']);
				$sysdescr = XSS_filter($_POST['sysdescr']);
				$workdescr = XSS_filter($_POST['workdescr']);
				if (!empty($customer) && !empty($fixid) && !empty($stype) && !empty($mtype) && !empty($start) && !empty($end) && !empty($main)) {
					$APP_sql = new APP_SQL();
					$data = array(
						"cid" => $customer,
						"wid" => $fixid,
						"srvtype" => $stype,
						"matype" => $mtype,
						"stime" => $start,
						"etime" => $end,
						"mainmbr" => $main,
						"submbr" => $sub,
						"sysinfo" => $sysdescr,
						"workinfo" => $workdescr,
						"engineer" => $_SESSION["Login_name"]
					);
					$APP_sql -> insertSrvs($data);
					$App_affected = $APP_sql -> affected();
					$APP_sql -> close();
					if ($App_affected == 1) {
						$result = array(
							"code" => 1,
							"message" =>  "添加成功"
						);
					} else {
						$result = array(
							"code" => 0,
							"message" =>  "添加失败"
						);
					}
				} else {
					$result = array(
						"code" => 0,
						"message" => "资料不全"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
				break;
			case "query":
				if (!empty($_POST['id'])) {
					$id = numeric_filter($_POST['id']); 
					header("Location: ?a=services&p=query&id={$id}");
				} else {
					$id = numeric_filter($_GET['id']);
				}
				if (empty($id)) {
					header("Status: 404");
					exit;
				}
				$APP_sql = new APP_SQL();
				$APP_result = $APP_sql -> getTableAllWhere("view_srvform","id",$id);
				$APP_sql -> close();
				APP_html_header();
?>
			<div id="APP_querySrvs">
				<div class="title_container"><img src="/images/leyoung.png" height="30px" /><span class="title_more">LY-QR-09-13</span></div>
				<h2>客  户 服 务 报 告 单</h2>
				<div class="title_container"><span class="title_more">单号_ _ _ _ _ _ _ _</span></div>
				<div>
					<table class="formtable">
						<tr>
							<td width=15%>客户名称: </td>
							<td id="APP_querySrvs_cname" width=40%><u><?php echo $APP_result['name'];?></u></td>
							<td>联 系 人：</td>
							<td id="APP_querySrvs_ccontact"><u><?php echo $APP_result['contact'];?></u></td>
						</tr>
						<tr>
							<td>地 址：</td>
							<td id="APP_querySrvs_caddr"><u><?php echo $APP_result['address'];?></u></td>
							<td>电 话：</td>
							<td id="APP_querySrvs_ctel"><u><?php echo $APP_result['tel'];?></u></td>
						</tr>
					</table>
				</div>
				<div><table><tr><td>客户报修/需求：</td><td><u><?php echo $APP_result['headline'];?></u></td><td></td><td></td></tr></table></div>
				<div>服务类型：</div>
				<div>
					<table class="formtable">
						<tr>
<?php
				$i = 1;
				$stype = array("新机保修","利银MA","厂商MA","无备件MA","保外服务");
				foreach ($stype as $stype_out) {
					if ($i == $APP_result['srvtype']) {
						$stype_out = "√{$stype_out}";
					}
?>
							<td><?php echo $stype_out;?></td>
<?php
					$i++;
				}
?>
						</tr>
					</table>
					<table class="formtable">
						<tr>
<?php
				$i = 1;
				$mtype = array("生产系统维护","备份系统维护","测试系统维护","备机维护");
				foreach ($mtype as $mtype_out) {
					if ($i == $APP_result['matype']) {
						$mtype_out = "√{$mtype_out}";
					}
?>
							<td><?php echo $mtype_out;?></td>
<?php
					$i++;
				}
?>
						</tr>
					</table>
				</div>
				<div>服务时间：</div>
				<div>
					<table class="formtable">
						<tr>
							<td>服务开始时间：<?php echo $APP_result['stime'];?></td>
							<td>服务结束时间：<?php echo $APP_result['etime'];?></td>
						</tr>
					</table>
				</div>
				<div>服务人员：</div>
				<div>
					<table class="formtable">
						<tr>
							<td>主要实施人员：<u><?php echo $APP_result['mainmbr'];?></u></td>
							<td>协助实施人员：<u><?php echo $APP_result['submbr'];?></u></td>
						</tr>
					</table>
				</div>
				<div>系统描述：</div>
				<div id ="APP_querySrvs_sysdescr" class="APP_querySrvs_border"><?php echo $APP_result['sysinfo'];?></div>
				<div>具体工作内容：</div>
				<div class="APP_querySrvs_border"><?php echo $APP_result['workinfo'];?></div>
				<div>操作步骤：</div>
				<div id="APP_querySrvs_opmethod" class="APP_querySrvs_border"><?php echo $APP_result['body'];?></div>
				<div><table><tr><td>客户评价：</td><td>口满意</td><td>口比较满意</td><td>口不满意</td><td></td><td></td><td></td></tr></table></div>
				<div>客户意见与建议：</div>
				<div id="APP_querySrvs_suggest"></div>
				<div>
					<table class="formtable">
						<tr>
							<td>客户签名：</td>
							<td></td>
							<td width=25%>工程师签名：</td>
							<td width=10%></td>
						</tr>
						<tr>
							<td>日期：</td>
							<td></td>
							<td>日期：</td>
							<td></td>
						</tr>
					</table>
				</div>
				<center>上海利银科技有限公司</center>
			</div>
			<button class="red" onclick="$('#APP_querySrvs').printArea()">打印服务报告单</button>
<?php
				APP_html_footer();
				break;

			case "":
				break;
		}
		break;
		exit;
	/**
	 * 功能 - > 服务报告单(end)
	 *
	 */
	/**
	 * 功能 - > 首页(start)
	 *
	 */
	case '':
		switch ($_GET['p']) {
			//处理总时间记录
			case "recordtime":
				if (!empty($_SESSION['index_recordtime'])) {
					exit;
				}
				$onwork = $_POST['onwork'];
				$overwork = $_POST['overwork'];
				$rest = $_POST['rest'];

				//对于非法传入的值全部置0
				if (!is_numeric($onwork)) {
					$onwork = 0;
				}
				if (!is_numeric($overwork)) {
					$overwork = 0;
				}
				if (!is_numeric($rest)) {
					$rest = 0;
				}

				$APP_sql = new APP_SQL();
				$APP_sql -> updateWorktime($onwork, $overwork, $rest, $_SESSION['Login_account']);
				$App_affected = $APP_sql -> affected();
				$APP_sql -> close();
				if ($App_affected == 1) {
					$result = array (
							"code" => 1
						);
				} else {
					$result = array (
							"code" => 0
						);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
				break;
			//处理总时间记录结束
			//处理每日工作时间
			case "workrecord":
				$post = $_POST['op'];
				$item = $_POST['item'];
				$APP_sql = new APP_SQL();
				if ($post == "in") {
					switch ($item) {
						case "1":
							$field = "comcheckin";
							$time = "09:00:00";
							$status = "1";
							$APP_sql -> updateWorkrecord($field, $time, $status);
							$App_affected = $APP_sql -> affected();
							$APP_sql -> close();
							if ($App_affected == 1) {
								$result = array (
										"code" => 1
									);
								header('Content-Type: application/json');
								echo json_encode($result);
								exit;
							}
							break;
						case "2":
							$field = "cencheckin";
							$time = "09:00:00";
							$status = "2";
							if (strtotime($_SESSION['workrecord_date']) <= strtotime($_SESSION['index_recordtime'])) {
								$weekend = -1;
							} else {
								$weekend = $_POST['weekend'];
							}
							if ($weekend == 1) {
								$APP_sql -> initcmt();
								$query = $APP_sql -> updateWorkrecord($field, $time, $status);
								if (!$query) {
									$APP_sql -> cmtroll();
								}
								$query = $APP_sql -> changeWorktime("onwork", "8");
								if (!$query) {
									$APP_sql -> cmtroll();
								} else {
									$App_affected = $APP_sql -> affected();
									$APP_sql -> cmtcommit();
								}
							} else {
								$APP_sql -> updateWorkrecord($field, $time, $status);
								$App_affected = $APP_sql -> affected();
							}
							$APP_sql -> close();
							if ($App_affected == 1) {
								$result = array (
										"code" => 1
									);
								header('Content-Type: application/json');
								echo json_encode($result);
								exit;
							}
							break;
						case "3":
							$field = "checkstatus";
							$time = "3";
							$status = "3";
							if (strtotime($_SESSION['workrecord_date']) <= strtotime($_SESSION['index_recordtime'])) {
								$weekend = -1;
							} else {
								$weekend = $_POST['weekend'];
							}
							if ($weekend == 0) {
								$APP_sql -> initcmt();
								$field = "comcheckin";
								$time = "00:00:00";
								$query = $APP_sql -> updateWorkrecord($field, $time, $status);
								if (!$query) {
									$APP_sql -> cmtroll();
								} else {
									$query = $APP_sql -> changeWorktime("rest", "8");
								}
								if (!$query) {
									$APP_sql -> cmtroll();
								} else {
									$App_affected = $APP_sql -> affected();
									$APP_sql -> cmtcommit();
								}
							} else {
								$APP_sql -> updateWorkrecord($field, $time, $status);
								$App_affected = $APP_sql -> affected();
							}
							$APP_sql -> close();
							if ($App_affected == 1) {
								$result = array (
										"code" => 1
									);
								header('Content-Type: application/json');
								echo json_encode($result);
								exit;
							}
							break;
					}
				} else if ($post == "out") {
					switch ($item) {
						case "1":
							$field = "comcheckout";
							$time = "17:15:00";
							$status = "4";
							$APP_sql -> updateWorkrecord($field, $time, $status);
							$App_affected = $APP_sql -> affected();
							$APP_sql -> close();
							if ($App_affected == 1) {
								$result = array (
										"code" => 1
									);
								header('Content-Type: application/json');
								echo json_encode($result);
								exit;
							}
							break;
						case "2":
							$time = $_POST['time'];
							$hour = $time;
							if ($time <= 0) {
								$timestamp = 17 * 3600;
							} else if ($time < 6) {
								$timestamp = $time * 3600 + 18 * 3600;
							} else {
								$timestamp = ($time - 2) * 3600 + 18 * 3600;
							}
							$field = "cencheckout";
							$datestamp = strtotime($_SESSION['workrecord_date']);
							$datestamp = $datestamp + $timestamp;
							$time = date("H:i:s",$datestamp);
							$status = "4";
							if (strtotime($_SESSION['workrecord_date']) <= strtotime($_SESSION['index_recordtime'])) {
								$hour = 0;
							}
							if ($hour > 0) {
								$APP_sql -> initcmt();
								$query = $APP_sql -> updateWorkrecord($field, $time, $status);
								if (!$query) {
									$APP_sql -> cmtroll();
								}
								$query = $APP_sql -> changeWorktime("overwork", $hour);
								if (!$query) {
									$APP_sql -> cmtroll();
								} else {
									$App_affected = $APP_sql -> affected();
									$APP_sql -> cmtcommit();
								}
							} else {
								$APP_sql -> updateWorkrecord($field, $time, $status);
								$App_affected = $APP_sql -> affected();
							}
							$APP_sql -> close();
							if ($App_affected == 1) {
								$result = array (
										"code" => 1
									);
								header('Content-Type: application/json');
								echo json_encode($result);
								exit;
							}
							break;
						case "3":
							$field = "checkstatus";
							$time = "4";
							$status = "4";
							$APP_sql -> updateWorkrecord($field, $time, $status);
							$App_affected = $APP_sql -> affected();
							$APP_sql -> close();
							if ($App_affected == 1) {
								$result = array (
										"code" => 1
									);
								header('Content-Type: application/json');
								echo json_encode($result);
								exit;
							}
							break;
					}
				} else if ($post == "inn") {
					switch ($item) {
						case "2":
							$field = "cencheckin";
							$time = "18:00:00";
							$status = "2";
							$APP_sql -> updateWorkrecord($field, $time, $status);
							$App_affected = $APP_sql -> affected();
							$APP_sql -> close();
							if ($App_affected == 1) {
								$result = array (
										"code" => 1
									);
								header('Content-Type: application/json');
								echo json_encode($result);
								exit;
							}
							break;
					}
				} else if ($post == "transfer") {
					switch ($item) {
						case "2":
							$field = "comcheckout";
							$time = "16:00:00";
							$status = "2";
							$APP_sql -> updateWorkrecord($field, $time, $status);
							$field = "cencheckin";
							$time = "16:00:00";
							$APP_sql -> updateWorkrecord($field, $time, $status);
							$App_affected = $APP_sql -> affected();
							$APP_sql -> close();
							if ($App_affected == 1) {
								$result = array (
										"code" => 1
									);
								header('Content-Type: application/json');
								echo json_encode($result);
								exit;
							}
							break;
					}
				}
				break;
			//处理每日工作时间结束
		}
		break;
	/**
	 * 功能 - > 首页(end)
	 *
	 */
	/**
	 * 功能 - > WIKI(start)
	 *
	 */
	case "wiki":
		//添加wiki
		switch ($_GET['p']) {
			case "add":
				$title = string_filter($_POST['title']);
				$subtype = numeric_filter($_POST['type']);
				$content = XSS_filter($_POST['content']);
				if ($title == "" || $subtype == "" || $content == "") {
					$result = array (
							"code" => 0,
							"message" => "文档添加失败"
						);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				}
				$APP_sql = new APP_SQL();
				$APP_sql -> insertWIKI($title, $subtype, $content);
				$App_affected = $APP_sql -> affected();
				$APP_sql -> close();
				if ($App_affected == 1) {
					$result = array (
							"code" => 1,
							"message" => "文档添加成功"
						);
				} else {
					$result = array (
							"code" => 0,
							"message" => "文档添加失败"
						);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
				break;
		}
		//添加wiki结束
		break;
	/**
	 * 功能 - > WIKI(end)
	 *
	 */
}
	
?>

