<?php
/**
 * 后端数据处理程序
 * 功能索引
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


$module_name = $_GET['a'];

switch ($module_name) {
	/**
	 * 模块 - > 账号管理模块
	 *
	 */
	case "staff":
		switch ($_GET['p']) {
			case "add":
				$name = string_filter($_POST['name']);
				$gender = numeric_filter($_POST['gender']);
				if ($_POST['department'] == $_SESSION['Login_section'] || $_SESSION['Login_section'] == "0") {
					$department = numeric_filter($_POST['department']);
				} else {
					$department = "";
				}
				$position = numeric_filter($_POST['position']);
				$tel = numeric_filter($_POST['tel']);
				$mail = string_filter($_POST['mail']);
				if (empty($name) || is_int($gender) || empty($department) || is_int($position) || empty($mail)) {
					$result = array(
						"code" => 0,
						"message" => "数据提交异常"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				} else {
					$APP_sql = new APP_SQL();
					$sql = "INSERT INTO `s_staff` (`name`,`gender`,`department`,`position`,`tel`,`mail`,`status`,`regist_time`) values ('{$name}', '{$gender}', '{$department}', '{$position}', '{$tel}', '{$mail}', '0', now());";
					$APP_sql -> userDefine($sql);
					$App_affected = $APP_sql -> affected();
					$APP_sql -> close();
				}
				if ($App_affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "信息已录入，请<button onclick=load_verifyStaff()>审核账号</button>"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				} else {
					$result = array(
						"code" => 0,
						"message" => "数据提交异常"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				}
				break;
			case "listverify":
?>
				<div class="title_container"><h1>待审核账号</h1></div><br />
				<table class="datatable">
					<tr>
						<th width=10%>姓名</th>
						<th width=17%>部门&职位</th>
						<th width=15%>电话</th>
						<th width=25%>email</th>
						<th>操作</th>
						<th>状态</th>
					</tr>
<?php
				$APP_sql = new APP_SQL();
				$App_verifyStaff = $APP_sql -> getTableAll("view_unLoginAuth");
				while ($App_verifyStaff_query = $APP_sql -> fetch_assoc($App_verifyStaff)) {
					$id = $App_verifyStaff_query['id'];
					$name = $App_verifyStaff_query['name'];
					$job = job_converter($App_verifyStaff_query['department'],$App_verifyStaff_query['position']);
					$tel = $App_verifyStaff_query['tel'];
					$mail = $App_verifyStaff_query['mail'];
					if (!empty($App_verifyStaff_query['authorizer'])) {
						$authorizer = "邮件已发送";
					} else {
						$authorizer = "";
					}
?>
					<tr class="verify_<?php echo $id; ?>">
						<td><?php echo $name;?></td>
						<td><?php echo $job;?></td>
						<td><?php echo $tel;?></td>
						<td><?php echo $mail;?></td>
						<td>
						<?php if ($authorizer == "") {?>
							<button class="btn" id="allowbtn_<?php echo $id; ?>" onclick="verifyStaff_allow(<?php echo $id.",'".$mail."'"; ?>)">允许</button>
						<?php } ?>
							<button class="btn blue" onclick="verifyStaff_deny(<?php echo $id; ?>)">拒绝</button>
						</td>
						<td class="authorizer_<?php echo $id; ?>"><?php echo $authorizer;?></td>
					</tr>
<?php
				}
?>

				</table>
<?php
				$APP_sql -> close();
				exit;
				break;
			case "allowverify":
				$id = numeric_filter($_POST['id']);
				$mail = string_filter($_POST['mail']);
				$start = mt_rand(3,10);
				$long = mt_rand(5,15);
				$authorizer = substr(sha1(time()),$start,$long);
				$APP_sql = new APP_SQL();
				$mailsubject = "OMM运维管理系统注册确认邮件";
				$mailbody = "您在利银运维管理系统的个人信息已经生成，请访问 http://192.168.235.251/valid.php?code=$authorizer 进行账号激活 ";
				if (send_mail($mail,$mailsubject,$mailbody) == "") {
					$APP_sql -> updateStaffAuthorizer($id,$authorizer);
					$App_affected = $APP_sql -> affected();
					if ($App_affected == 1) {
						$result = array(
							"code" => 1,
							"authorizer" => "邮件发送成功"
						);
					} else {
						$result = array(
							"code" => 0,
							"authorizer" => "请重发邮件"
						);
					}
				} else {
					$result = array(
						"code" => 0,
						"authorizer" => "邮件发送失败"
					);
				}
				$APP_sql -> close();
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
				break;
			case "denyverify":
				$id = numeric_filter($_POST['id']);
				$APP_sql = new APP_SQL();
				$APP_sql -> deleteNoStaff($id);
				$App_affected = $APP_sql -> affected();
				if ($App_affected == 1) {
					$result = array(
						"code" => 1
					);
					header('Content-Type: application/json');
					echo json_encode($result);
				}
				$APP_sql -> close();
				exit;
				break;
			default:
				break;
		}
		break;

	/**
	 * 功能 - > 其他
	 *
	 */
	case "customer":
		switch ($_GET['p']) {
			case "add":
				$name = string_filter($_POST['name']);
				$nickname = string_filter($_POST['nickname']);
				$contact = string_filter($_POST['contact']);
				$tel = numeric_filter($_POST['tel']);
				$addr = string_filter($_POST['addr']);
				if (empty($name)) {
					$result = array(
						"code" => 0,
						"message" => "数据提交异常"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				} else {
					$APP_sql = new APP_SQL();
					$sql = "INSERT INTO `s_customer` (`name`,`nickname`,`contact`,`tel`,`address`,`status`,`regtime`) values ('{$name}', '{$nickname}', '{$contact}', '{$tel}', '{$addr}','1',now());";
					$APP_sql -> userDefine($sql);
					$App_affected = $APP_sql -> affected();
					$APP_sql -> close();
				}
				if ($App_affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "客户信息已录入"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				} else {
					$result = array(
						"code" => 0,
						"message" => "数据提交异常"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				}
				break;
			case "freeze":
				$id = numeric_filter($_POST['id']);
				if (!empty($id)) {
					if ($_SESSION['Login_section'] == 0) {
						$APP_sql = new APP_SQL();
						$sql = "UPDATE `s_customer` SET `display` = '0', `status` = (status + 1)%2 WHERE `id` = '{$id}';";
						$APP_sql -> userDefine($sql);
						$affected = $APP_sql -> affected();
						$APP_sql -> close();
					} else {
						$APP_sql = new APP_SQL();
						$sql = "SELECT * from `s_customer` where `id` = '{$id}';";
						$query = $APP_sql -> userDefine($sql) -> fetch_assoc();
						if ($query['status'] % 2 == 1) {
							$sql = "UPDATE `s_customer` SET `display` = '0', `status` = (status + 1)%2 WHERE `id` = '{$id}';";
							$APP_sql -> userDefine($sql);
							$affected = $APP_sql -> affected();
						}
						$APP_sql -> close();
					}
					if ($affected == 1) {
						$result = array(
							"code" => 1,
							"message" => "修改成功"
						);
						header('Content-Type: application/json');
						echo json_encode($result);
						exit;
					} else {
						$result = array(
							"code" => 0,
							"message" => "貌似进行了非法的更改，已忽略"
						);
						header('Content-Type: application/json');
						echo json_encode($result);
						exit;
					}
				}
				break;
			case "display":
				$id = numeric_filter($_POST['id']);
				if (!empty($id)) {
					$APP_sql = new APP_SQL();
					$sql = "UPDATE `s_customer` SET `display` = (display + 1)%2 WHERE `id` = '{$id}' AND `status` = '1';";
					$APP_sql -> userDefine($sql);
					$affected = $APP_sql -> affected();
					$APP_sql -> close();
					if ($affected >=1) {
						$result = array(
							"code" => 1,
							"message" => "已更改服务报告单显示状态"
						);
						header('Content-Type: application/json');
						echo json_encode($result);
						exit;
					} else {
						$result = array(
							"code" => 0,
							"message" => "更改显示状态失败"
						);
						header('Content-Type: application/json');
						echo json_encode($result);
						exit;
					}
				}
				break;
			case "query":
				if ($_SESSION['query']['customer'] != "" && empty($_POST['name'])) {
					$customer = $_SESSION['query']['customer'];
				} else {
					$_SESSION['query']['customer'] = $customer = string_filter($_POST['name']);
				}
				$id = numeric_filter($_GET['id']);
				if (!empty($customer) || !empty($id)) {
					if (!empty($id)) {
						$APP_sql = new APP_SQL();
						$App_queryCustomer = $APP_sql -> getCustomerMeta($id);
						$APP_sql -> close();
						$name = $App_queryCustomer['name'];
						$nickname = $App_queryCustomer['nickname'];
						$contact = $App_queryCustomer['contact'];
						$tel = $App_queryCustomer['tel'];
						$address = $App_queryCustomer['address'];
						$regtime = $App_queryCustomer['regtime'];
						if (empty($name)) {
							$name = $nickname = $contact = $tel = $address = $regtime = "信息不存在或无权查看";
						}
						APP_html_header();
?>
			<table class="datatable">
				<tr>
					<td>客户编号</td>
					<td colspan=3><?php echo $id;?></td>
				</tr>
				<tr>
					<td width=15%>客户名称</td>
					<td width=35%><?php echo $name;?></td>
					<td width=15%>客户简称</td>
					<td><?php echo $nickname;?></td>
				</tr>
				<tr>
					<td>客户地址</td>
					<td colspan=3><?php echo $address;?></td>
				</tr>
				<tr>
					<td>联系人</td>
					<td><?php echo $contact;?></td>
					<td>联系电话</td>
					<td><?php echo $tel;?></td>
				</tr>
				<tr>
					<td>登记时间</td>
					<td colspan=3><?php echo $regtime;?></td>
				</tr>
			</table>
<?php
						APP_html_footer();
					} else {
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
						$APP_sql = new APP_SQL();
						$App_listCustomer = $APP_sql -> getCustomerQueryList($customer,$start,$records);
						$App_countCustomer = $APP_sql -> getCustomerQueryList($customer);
						$App_result_rows = $App_countCustomer -> num_rows;
						$APP_sql -> close();
						APP_html_header();
?>
			<div class="title_container">
				<span class="title_more">
					<form id="APP_queryStaff_form" action="?a=customer&p=query" method="post">
						<input type="text" size="10" name="name" id="APP_queryCustomer_name" placeholder="客户名称" />
						<input type="submit" value="查询" />
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
					客户别名<input type="text" name="nickname" size="10" id="APP_newCustomer_nickname" />
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
						while ($App_listCustomer_query = $App_listCustomer -> fetch_assoc()) {
							$id = $App_listCustomer_query['id'];
							$url = "?a=customer&p=query&id=$id";
							$name = $App_listCustomer_query['name'];
							$contacter = $App_listCustomer_query['contact'];
							$tel = $App_listCustomer_query['tel'];
							$address = $App_listCustomer_query['address'];
							$status = $App_listCustomer_query['status'];
							if ($status == 1) {
								$status_text = "断开合作";
							} else {
								$status_text = "重新合作";
							}
							$display = $App_listCustomer_query['display'];
							if ($display == 1) {
								$display_text = "前端隐藏";
							} else {
								$display_text = "前端显示";
							}
							if ($_SESSION['Login_section'] == 0 || $status % 2 == 1) {
?>
					<tr class="<?php echo "APP_customer_".$id; ?>">
						<input type="hidden" value="<?php echo $App_listCustomer_query['nickname'];?>" />
						<td class="customer_id"><a href="<?php echo $url;?>" target="_blank"><?php echo $id;?></a></td>
						<td class="customer_name"><?php echo $name;?></td>
						<td class="customer_contacter"><?php echo $contacter;?></td>
						<td class="customer_tel"><?php echo $tel;?></td>
						<td class="customer_address"><?php echo $address;?></td>
						<td>
							<button class="APP_customer_chdata">资料修改</button>
<?php 
								if ($_SESSION['Login_section'] == 0) {
?>
							<button onclick="chstatCustomer(<?php echo $id;?>)"><?php echo $status_text;?></button>
<?php 
								} else {
?>
							<button onclick="chDisplay(<?php echo $id;?>)"><?php echo $display_text;?></button>
<?php
								}
?>
					</tr>
<?php
							}
						}
						if (($App_result_rows / $records) > floor($App_result_rows / $records) || $App_result_rows == 0) {
							$pages = floor($App_result_rows / $records) + 1;
						} else {
							$pages = $App_result_rows / $records;
						}
						$url_fp = "?a=customer&p=query&record=".$records;
						if ($curpage == 1) {
							$prepage = $curpage;
						} else {
							$prepage = $curpage - 1;
						}
						if ($curpage >= $pages) {
							$nxpage = $pages;
							$prepage = $pages;
						} else {
							$nxpage = $curpage + 1;
						}
						$url_pp = "?a=customer&p=query&record=".$records."&page=".$prepage;
						$url_np = "?a=customer&p=query&record=".$records."&page=".$nxpage;
						$url_lp = "?a=customer&p=query&record=".$records."&page=".$pages;
?>
				</table>
				<p></p>
				<center><a href="<?php echo $url_fp;?>"><<</a><a href="<?php echo $url_pp;?>"><</a><a href="<?php echo $url_np;?>">></a><a href="<?php echo $url_lp;?>">>></a></center>
				<center><?php echo $_SERVER['PHP_SCRIPT'];?></center>
			</div>
<?php
						APP_html_footer();
					}
				} else {
					header("Status: 404");
					exit;
				}
				break;
			case "chdata":
				$id = numeric_filter($_POST['id']);
				$name = string_filter($_POST['name']);
				$contacter = string_filter($_POST['contacter']);
				$tel = string_filter($_POST['tel']);
				$addr = string_filter($_POST['addr']);
				//此处包含一个后门：
				//当用户已经不再合作后，管理员虽然看不见，但是仍然可以修改该客户资料
				//补丁暂留，用于演示。
				if (!empty($id) && !empty($name)) {
					$APP_sql = new APP_SQL();
					$sql = "UPDATE `s_customer` SET `nickname` = '{$name}',`contact` = '{$contacter}',`tel` = '{$tel}',`address` = '{$addr}' WHERE `id` = '{$id}';";
					$APP_sql -> userDefine($sql);
					$affected = $APP_sql -> affected();
					$APP_sql -> close();
					if ($affected >=1) {
						$result = array(
							"code" => 1,
							"message" => "OMM：客户资料已更新"
						);
						header('Content-Type: application/json');
						echo json_encode($result);
						exit;
					} else {
						$result = array(
							"code" => 0,
							"message" => "OMM：数据更新失败，请重试"
						);
						header('Content-Type: application/json');
						echo json_encode($result);
						exit;
					}
				} else {
					$result = array(
						"code" => 1,
						"message" => "OMM：遇到非法数据"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				}
				break;
		}
		break;
	/**
	 * 模块 - > 设备管理模块
	 *
	 */
	case "device":
		//选择操作类型
		switch ($_GET["p"]) {
			//添加设备
			case "add":
				//接收POST变量并过滤
				$type = numeric_filter($_POST["type"]);
				$cid = numeric_filter($_POST["customer"]);
				$name = string_filter($_POST["name"]);
				$sn = string_filter($_POST["sn"]);

				//检查必需性数据
				if (empty($type) || empty($cid) || empty($name) || empty($sn)) {
					$result = array(
						"code" => 0,
						"message" => "数据提交异常"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				} else {
					//接收参数性数据
					$os = string_filter($_POST["os"]);
					$fw = string_filter($_POST["fw"]);
					$cpu = string_filter($_POST["cpu"]);
					$ram = string_filter($_POST["ram"]);
					$disk = string_filter($_POST["disk"]);
					$raid = string_filter($_POST["raid"]);
					$hba = string_filter($_POST["hba"]);
					$bat = string_filter($_POST["bat"]);

					//转换数据
					switch ($type) {
						case "1":
							$type = "UNIX服务器";
						break;
						case "1":
							$type = "PC服务器";
						break;
						case "1":
							$type = "PC";
						break;
						case "1":
							$type = "存储设备";
						break;
						case "1":
							$type = "网络设备";
						break;
						case "1":
							$type = "拓展柜";
						break;
						default:
							$type = "其他";
						break;
					}
					$bat = date("Y-m-d",strtotime($bat));
					$cfg = "[CPU=$cpu][RAM=$ram][DISK=$disk][RAID=$raid][HBA=$hba][BAT=$bat]";

					//插入数据
					$APP_sql = new APP_SQL();
					$sql = "INSERT INTO `s_device` () values ('','{$type}','{$name}','{$sn}', '{$os}', '{$fw}','','','{$cid}','','','{$cfg}',now(),'','1');";
					$APP_sql -> userDefine($sql);
					$App_affected = $APP_sql -> affected();
					$APP_sql -> close();
				}
				if ($App_affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "设备信息已录入"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				} else {
					$result = array(
						"code" => 0,
						"message" => "数据提交异常"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				}
				break;
			//.....添加设备
		}
		break;
	default:
		break;
}
exit;

?>