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


$action = explode('_',$_GET['a']);
$module_name = $action[1];

switch ($module_name) {
	/**
	 * 功能 - > 1
	 *
	 */
	case "member":
		switch($_GET['p']){
			case "add":
				$name = string_filter($_POST['name']);
				$gender = numeric_filter($_POST['gender']);
				$department = numeric_filter($_POST['department']);
				$position = numeric_filter($_POST['position']);
				$tel = numeric_filter($_POST['tel']);
				if (empty($name) || empty($gender) || empty($department) || empty($position)) {
					$result = array(
						"code" => 0,
						"message" => "数据提交异常"
					);
					header('Content-Type: application/json');
					echo json_encode($result);
					exit;
				} else {
					$APP_sql = new APP_SQL();
					$sql = "INSERT INTO `s_staff` (`name`,`gender`,`department`,`position`,`tel`,`status`,`regist_time`) values ('{$name}', '{$gender}', '{$department}', '{$position}', '{$tel}', '0', now());";
					$APP_sql -> userDefine($sql);
					$App_affected = $APP_sql -> affected();
					$APP_sql -> close();
				}
				if ($App_affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "新账号已生成，请去<button onclick=load_verifyStaff()>审核账号</button>"
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
				<table class="datatable">
					<tr>
						<th>姓名</th>
						<th>性别</th>
						<th>部门&职位</th>
						<th>电话</th>
						<th>验证码</th>
						<th>操作</th>
					</tr>
<?php
				$APP_sql = new APP_SQL();
				$App_verifyStaff = $APP_sql -> getTableAll("view_unLoginAuth");
				while ($App_verifyStaff_query = $APP_sql -> fetch_assoc($App_verifyStaff)) {
					$id = $App_verifyStaff_query['id'];
					$name = $App_verifyStaff_query['name'];
					$gender = gender_converter($App_verifyStaff_query['gender']);
					$job = job_converter($App_verifyStaff_query['department'],$App_verifyStaff_query['position']);
					$tel = $App_verifyStaff_query['tel'];
					$authorizer = $App_verifyStaff_query['authorizer'];
?>
					<tr class="verify_<?php echo $id; ?>">
						<td><?php echo $name;?></td>
						<td><?php echo $gender;?></td>
						<td><?php echo $job;?></td>
						<td><?php echo $tel;?></td>
						<td class="authorizer_<?php echo $id; ?>"><?php echo $authorizer;?></td>
						<td>
						<?php if ($authorizer == "") {?>
							<button class="btn" id="allowbtn_<?php echo $id; ?>" onclick="verifyStaff_allow(<?php echo $id; ?>)">允许</button>
						<?php } ?>
							<button class="btn red" onclick="verifyStaff_deny(<?php echo $id; ?>)">拒绝</button>
						</td>
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
				$start = mt_rand(3,10);
				$long = mt_rand(5,10);
				$authorizer = substr(sha1(time()),$start,$long);
				$APP_sql = new APP_SQL();
				$APP_sql -> updateStaffAuthorizer($id,$authorizer);
				$App_affected = $APP_sql -> affected();
				$APP_sql -> close();
				if ($App_affected == 1) {
					$result = array(
						"authorizer" => $authorizer
					);
					header('Content-Type: application/json');
					echo json_encode($result);
				}
				$APP_sql -> close();
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