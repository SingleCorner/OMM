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
							"content" => $query['body'],
							"requirement" => $query['headline']
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
				$requirement = string_filter($_POST['requirement']);
				$stype = numeric_filter($_POST['stype']);
				$mtype = numeric_filter($_POST['mtype']);
				$start = date("Y-m-d\ H:i:s",strtotime($_POST['start']));
				$end = date("Y-m-d\ H:i:s",strtotime($_POST['end']));
				$main = string_filter($_POST['main']);
				$sub = string_filter($_POST['sub']);
				$accompany = string_filter($_POST['accompany']);
				$sysdescr = XSS_filter($_POST['sysdescr']);
				$workdescr = XSS_filter($_POST['workdescr']);
				if (empty($stype) || empty($mtype)) {
					$stype = $mtype = -1;
				}
				if (!empty($customer) && !empty($fixid) && !empty($stype) && !empty($mtype) && !empty($start) && !empty($end) && !empty($main)) {
					$APP_sql = new APP_SQL();
					$data = array(
						"cid" => $customer,
						"wid" => $fixid,
						"requirement" => $requirement,
						"srvtype" => $stype,
						"matype" => $mtype,
						"stime" => $start,
						"etime" => $end,
						"mainmbr" => $main,
						"submbr" => $sub,
						"accompany" => $accompany,
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
				} else if (!empty($_GET['id'])) {
					$id = numeric_filter($_GET['id']);
					$APP_sql = new APP_SQL();
					$APP_result = $APP_sql -> getTableAllWhere("view_srvform","id",$id);
					if ($APP_result['id'] == "") {
						header("Location: ?a=services&CannotFindSevice");
					}
					$APP_sql -> close();
					APP_html_header();
?>
			<div id="APP_querySrvs">
				<div class="title_container"><img src="/images/leyoung.png" height="30px" /><span class="title_more">LY-QR-09-13</span></div>
				<h2>客 户 服 务 报 告 单</h2>
				<div class="title_container">&nbsp;<span class="title_more">单号_ _ _ _ _ _ _ _ _ _ _</span></div>
				<div>客 户 信 息 ：</div>
				<div>
					<table class="formtable">
						<tr>
							<td width=15%>客户名称：</td>
							<td id="APP_querySrvs_cname"><u><?php echo $APP_result['name'];?></u></td>
							<td width=15%>联 系 人：</td>
							<td id="APP_querySrvs_ccontact" width=15%><u><?php echo $APP_result['contact'];?></u></td>
						</tr>
						<tr>
							<td>地&nbsp;&nbsp;&nbsp;&nbsp;址：</td>
							<td id="APP_querySrvs_caddr"><u><?php echo $APP_result['address'];?></u></td>
							<td>电&nbsp;&nbsp;&nbsp;&nbsp;话：</td>
							<td id="APP_querySrvs_ctel"><u><?php echo $APP_result['tel'];?></u></td>
						</tr>
					</table>
				</div>
				<div><table><tr><td width=20%><strong>客户报修/需求：</strong></td><td><u><?php echo $APP_result['headline'];?></u></td></tr></table></div>
				<div>服务类型：</div>
				<div>
					<table class="formtable">
						<tr>
<?php
				$i = 1;
				$stype = array("保内服务","利银MA","厂商MA","无备件MA","保外服务");
				foreach ($stype as $stype_out) {
					if ($i == $APP_result['srvtype']) {
						$stype_out = "√{$stype_out}";
					} else {
						$stype_out = "口{$stype_out}";
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
				$mtype = array("生产系统维护","备份系统维护","测试开发系统维护","备机维护");
				foreach ($mtype as $mtype_out) {
					if ($i == $APP_result['matype']) {
						$mtype_out = "√{$mtype_out}";
					} else {
						$mtype_out = "口{$mtype_out}";
					}
?>
							<td><?php echo $mtype_out;?></td>
<?php
					$i++;
				}
?>
							<td></td>
						</tr>
					</table>
				</div>
				<div>服务时间：</div>
				<div>
					<table class="formtable">
						<tr>
							<td>服务开始时间：<u><?php echo date("Y年m月d日 H时i分s秒",strtotime($APP_result['stime']));?></u></td>
							<td>服务结束时间：<u><?php echo date("Y年m月d日 H时i分s秒",strtotime($APP_result['etime']));?></u></td>
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
				<div>陪同人员：</div>
				<div>
					<table class="formtable">
						<tr>
							<td>客户陪同人 ： <u><?php echo $APP_result['accompany'];?></u></td>
						</tr>
					</table>
				</div>
				<div>系统描述：</div>
				<div id ="APP_querySrvs_sysdescr" class="APP_querySrvs_border"><?php echo $APP_result['sysinfo'];?></div>
				<div>具体工作内容：</div>
				<div id="APP_querySrvs_workdescr" class="APP_querySrvs_border"><?php echo $APP_result['workinfo'];?></div>
				<div>操作步骤：</div>
				<div id="APP_querySrvs_opmethod" class="APP_querySrvs_border"><?php echo $APP_result['body'];?></div>
				<div><table><tr><td>客户评价：</td><td width=12%>口满意</td><td>口比较满意</td><td>口不满意</td><td></td><td></td><td></td></tr></table></div>
				<div>客户意见与建议：</div>
				<div id="APP_querySrvs_suggest"></div>
				<div>
					<table class="formtable">
						<tr>
							<td width=14%>客户签名：</td>
							<td>___________________</td>
							<td width=15%>工程师签名：</td>
							<td width=20%>___________________</td>
						</tr>
						<tr>
							<td>日&nbsp;&nbsp;&nbsp;&nbsp;期：</td>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;年&nbsp;&nbsp;&nbsp;月&nbsp;&nbsp;&nbsp;日</td>
							<td>日&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;期：</td>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;年&nbsp;&nbsp;&nbsp;月&nbsp;&nbsp;&nbsp;日</td>
						</tr>
					</table>
				</div>
				<center>上海利银科技有限公司</center>
			</div>
<?php
			if (isset($_GET['print'])) {
?>
			<script>$('#APP_querySrvs').jqprint();</script>
<?php
			}
?>
			<button class="red" onclick="$('#APP_querySrvs').jqprint()">打印服务报告单</button>
<?php
					APP_html_footer();
				} else if (!empty($_POST['engineer'])) {
					header("Location: ?a=services&p=query&engineer={$_POST['engineer']}");
				} else if (!empty($_GET['engineer'])) {
					$engineer = string_filter($_GET['engineer']);
					APP_html_header();
?>
			<div class="title_container">
				<span class="title_more">
					<form id="APP_querySrvs_form" action="?a=services&p=query" method="post">
						<input type="text" size="10" name="id" id="APP_querySrvs_id" placeholder="服务编号" />
						<input type="text" size="10" name="engineer" id="APP_querySrvs_engineer" placeholder="服务工程师" />
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
						<th width=8%>ID</th>
						<th width=25%>服务需求</th>
						<th width=20%>服务时间</th>
						<th width=15%>实施工程师</th>
						<th width=10%>陪同人</th>
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
			$query = $APP_sql -> getServicesQueryList($engineer,$start,$records);
			$APP_countSrvs = $APP_sql ->getServicesList($engineer);
			$App_result_rows = $APP_countSrvs -> num_rows;
			$APP_sql -> close();
			if (($App_result_rows / $records) > floor($App_result_rows / $records) || $App_result_rows == 0) {
				$pages = floor($App_result_rows / $records) + 1;
			} else {
				$pages = $App_result_rows / $records;
			}
			$url_fp = "?a=services&p=query&engineer={$engineer}";
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
			$url_pp = "?a=services&p=query&engineer={$engineer}&page={}".$prepage;
			$url_np = "?a=services&p=query&engineer={$engineer}&page=".$nxpage;
			$url_lp = "?a=services&p=query&engineer={$engineer}&page=".$pages;
			while ($APP_result = $query -> fetch_assoc()) {
				$id = $APP_result['id'];
				$need = $APP_result['headline'];
				$start = date("Y-m-d",strtotime($APP_result['stime']));
				$end = date("Y-m-d",strtotime($APP_result['etime']));
				$engineer = $APP_result['mainmbr'];
				$accompany = $APP_result['accompany'];
?>
					<tr>
						<td><?php echo $id;?></td>
						<td><?php echo $need;?></td>
						<td><?php echo $start." 至 ".$end;?></td>
						<td><?php echo $engineer;?></td>
						<td><?php echo $accompany;?></td>
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
					APP_html_footer();
				} else {
					header("Location: ?a=services");
					exit;
				}
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
						case "5":
							$field = "othcheckin";
							$time = "10:00:00";
							$status = "5";
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
						case "5":
							$field = "othcheckout";
							$time = "16:00:00";
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
							$time = "16:00:00";
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
						case "1":
							$field = "comcheckin";
							$time = "13:00:00";
							$status = "1";
							if (strtotime($_SESSION['workrecord_date']) <= strtotime($_SESSION['index_recordtime'])) {
								$weekend = -1;
							} else {
								$weekend = $_POST['weekend'];
							}
							if ($weekend == 0) {
								$query = $APP_sql -> updateWorkrecord($field, $time, $status);
								if (!$query) {
									$APP_sql -> cmtroll();
								} else {
									$query = $APP_sql -> changeWorktime("rest", "-4");
								}
								if (!$query) {
									$APP_sql -> cmtroll();
								} else {
									$App_affected = $APP_sql -> affected();
									$APP_sql -> cmtcommit();
								}
							} else {
								$query = $APP_sql -> updateWorkrecord($field, $time, $status);
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
						case "1":
							$field = "othcheckout";
							$time = "13:00:00";
							$status = "1";
							$APP_sql -> updateWorkrecord($field, $time, $status);
							$field = "comcheckin";
							$time = "13:00:00";
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
						case "3":
							$field = "comcheckout";
							$time = "13:00:00";
							$status = "4";
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
									$query = $APP_sql -> changeWorktime("rest", "4");
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

