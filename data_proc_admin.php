<?php
/**
 * 后端数据处理程序
 * 功能索引
 *	1.添加成员
 *	2.
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
	 * 功能 - > 1
	 *
	 */
	case "staff":
		switch($_GET['p']){
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
				$APP_sql -> updateStaffAuthorizer($id,$authorizer);
				$App_affected = $APP_sql -> affected();
				$APP_sql -> close();
				if ($App_affected == 1) {
					$mailsubject = "OMM运维管理系统注册确认邮件";
					$mailbody = "您在利银运维管理系统的个人信息已经生成，请访问 http://192.168.235.251/valid.php?code=$authorizer 进行账号激活 ";
					if (send_mail($mail,$mailsubject,$mailbody) == "") {
						$result = array(
							"code" => 1,
							"authorizer" => "邮件发送成功"
						);
						header('Content-Type: application/json');
						echo json_encode($result);
					} else {
						$result = array(
							"code" => 0,
							"authorizer" => "邮件发送失败"
						);
						header('Content-Type: application/json');
						echo json_encode($result);
					}
				}
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
	default:
		break;
}
exit;
?>