<?php
/**
 * 前端数据处理程序
 * 功能索引
 *	1.修改密码
 *	2.服务报告单
 *	3.WIKI
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
	 * 功能 - > 修改密码
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
	 * 功能 - > 服务报告单
	 *
	 */
	case 'services':
		echo 123;
		break;
		exit;
	/**
	 * 功能 - > 首页
	 *
	 */
	case '':
		switch ($_GET['p']) {
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
								$weekend = $_POST['weekend'];
							} else {
								$weekend = -1;
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
								$query = $APP_sql -> updateWorkrecord($field, $time, $status);
								if (!$query) {
									$APP_sql -> cmtroll();
								}
								$query = $APP_sql -> changeWorktime("rest", "8");
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
							$time = "17:00:00";
							$status = "2";
							$APP_sql -> updateWorkrecord($field, $time, $status);
							$field = "cencheckin";
							$time = "17:00:00";
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
		}
		break;
}
	
?>

