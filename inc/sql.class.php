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
 * 数据库操作类
 *
 */
class APP_SQL {
	
	private $db;
	
	/**
	 * 构造函数
	 */
	public function __construct() {
		$this -> _connect();
	}
	
	/**
	 * 连接数据库
	 *
	 * @access private
	 *
	 * @return void
	 */
   	private function _connect() {
		$this -> db = new mysqli(__DB_HOST__, __DB_USER__, __DB_PASSWD__, __DB__);
		if ($this -> db -> connect_errno) {
			die("您无权限访问数据库");
			return false;
		}
		
		$this -> db -> query('SET NAMES UTF8;');
	}
	/**
	 * 关闭数据库连接
	 */
	public function close() {
		return $this -> db -> close();
	}
	
	//通用SQL
	/**
	 * 查询 -> 全表数据
	 */
	public function getTableAll($value_table, $start = -1, $count = -1) {
		$sql = "SELECT * FROM `{$value_table}`";
		if ($start >= 0 && $count > 0) {
			$sql .= " LIMIT {$start},{$count}";
		}
		$sql .= ";";
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 查询 -> 单条件数据
	 */
	public function getTableAllWhere($value_table, $field, $field_value) {
		$sql="SELECT * FROM `{$value_table}` WHERE `{$field}` = '{$field_value}';";
		$query = $this -> db -> query($sql);
		return $query -> fetch_assoc();
	}
	/**
	 * 更新 -> 单列数据
	 */
	public function updateTable($table, $field, $field_value, $col, $col_value) {
		$sql = "UPDATE `{$table}` SET `{$field}` = '{$field_value}' WHERE `{$col}` = '{$col_value}';";
		$query = $this -> db -> query($sql);
		return $query -> fetch_assoc();
	}
	/**
	 * 执行事务处理
	 */
	public function transationSQL() {
		$this -> db -> autocommit(false);
		$sql = func_get_args();
		foreach ($sql as $query) {
			$result = $this -> db -> query($query);
			if (!$result) {
				$check = "1";
			}
		}
		if ($check == "1") {
			$this -> db -> rollback();
		} else {
			$this -> db -> commit();
			return "commit";
		}
	}
	/**
	 * 关闭自动提交，初始化事务
	 */
	public function initcmt() {
		return $this -> db -> autocommit(false);
	}
	/**
	 * 事务回滚
	 */
	public function cmtroll() {
		return $this -> db -> rollback();
	}
	/**
	 * 事务提交
	 */
	public function cmtcommit() {
		return $this -> db -> commit();
	}

	/**
	 * 查询上次query影响行数
	 */
	public function affected() {
		return $this -> db -> affected_rows;
	}	
	/**
	 * 查询结果数组化
	 */
	public function fetch_assoc($query) {
		return $query -> fetch_assoc();
	}	
	/**
	 * 自定义SQL（临时调用）
	 */
	public function userDefine($sql){
		return $this -> db -> query($sql);
	}




	//专用功能SQL
	/**
	 * 登陆验证
	 *
	 * @access public
	 *
	 * @param string $value_user 用户名
	 *
	 * @return array
	 */
	public function LoginAuth($value_user) {
		$sql = "SELECT * FROM `view_LoginAuth` WHERE `account` = '{$value_user}';";
		$query = $this -> db -> query($sql);
		return $query -> fetch_assoc();
	}


	/**
	 * 更新 -> 修改用户密码
	 */
	public function updateLoginPasswd($newpasswd, $account) {
		$sql = "UPDATE `view_LoginAuth` SET `passwd` = '{$newpasswd}' WHERE `account` = '{$account}';";
		return $this -> db -> query($sql);
	}
	/**
	 * 更新 -> 生成新用户授权码
	 */
	public function updateStaffAuthorizer($id, $authorizer) {
		$sql = "UPDATE `view_unLoginAuth` SET `authorizer` = '{$authorizer}' WHERE `id` = '{$id}';";
		return $this -> db -> query($sql);
	}
	/**
	 * 更新 -> 个性化用户名检查
	 */
	public function updateAccountCheck($id, $account) {
		$sql = "UPDATE `s_staff` SET `account` = '{$account}' WHERE `id` = '{$id}';";
		return $this -> db -> query($sql);
	}

	/**
	 * 更新 -> 首次设置工作时间
	 */
	public function updateWorktime($onwork, $overwork, $rest, $account) {
		$sql = "UPDATE `s_worktime` SET `onwork` = '{$onwork}',`overwork` = '{$overwork}',`rest` = '{$rest}',`recordtime` = current_date() WHERE `account` = '{$account}';";
		return $this -> db -> query($sql);
	}

	/**
	 * 更新 -> 工作时间记录
	 */
	public function updateWorkrecord($field, $time, $status) {
		$account = $_SESSION['Login_account'];
		$date = date("Y-m-d",$time);
		$sql = "UPDATE `s_check` SET `{$field}` = '{$time}',`checkstatus` = '{$status}' WHERE `account` = '{$account}' AND `date` = '{$_SESSION['workrecord_date']}';";
		return $this -> db -> query($sql);
	}
	/**
	 * 更新 -> 调整工作时间
	 */
	public function changeWorktime($field, $time) {
		$account = $_SESSION['Login_account'];
		$sql = "UPDATE `s_worktime` SET `{$field}` = {$field} + {$time} WHERE `account` = '{$account}';";
		return $this -> db -> query($sql);
	}



	/**
	 * 插入 -> 工作记录插入
	 */
	public function insertWorkrecord($date) {
		$account = $_SESSION['Login_account'];
		$sql = "INSERT INTO `s_check` (`account`,`date`,`checkstatus`) values ('{$account}','{$date}','0');";
		return $this -> db -> query($sql);
	}
	/**
	 * 插入 -> wiki
	 */
	public function insertWIKI($title, $subtype, $content) {
		$name = $_SESSION['Login_name'];
		$sql = "INSERT INTO `s_wiki` (`type`,`subtype`,`headline`,`body`,`owner`) values ('4','{$subtype}','{$title}','{$content}','{$name}');";
		return $this -> db -> query($sql);
	}
	/**
	 * 插入 -> 服务报告单
	 */
	public function insertSrvs($data) {
		foreach ($data as $field => $value) {
			$sql_field .= ", `{$field}`";
			$sql_value .= ", '{$value}'"; 
		}
		$sql_field = "`id`".$sql_field;
		$sql_value = "''".$sql_value;
		$sql = "INSERT INTO `s_services` ({$sql_field}) values ({$sql_value});";
		return $this -> db -> query($sql);
	}


	/**
	 * 查询 -> 列出所有账号
	 */
	public function getStaffList() {
		//$sql = "create or replace view `view_listStaff` as select a.id,a.name,a.department,a.position,a.tel,a.regist_time,a.status,b.authority,b.timeout from s_staff as a,s_authority as b where a.account = b.account ORDER BY `status` DESC,`regist_time`;"
		if ($_SESSION['Login_section'] == 0) {
			$sql = "SELECT * from `view_listStaff`;";
		} else {
			$sql = "SELECT * from `view_listStaff` WHERE `department` = '{$_SESSION['Login_section']}';";
		}
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 查询 -> 列出客户
	 * 部门管理可查看合作中客户，超级管理可查看所有客户
	 */
	public function getCustomerList($start,$records) {
		if ($_SESSION['Login_section'] == 0) {
			$sql = "SELECT * from `s_customer`";
		} else {
			$sql = "SELECT * from `s_customer` WHERE `status` = 1";
		}
		if (isset($start)) {
			$sql .= " LIMIT {$start},{$records};";
		} else {
			$sql .= ";";
		}
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 查询 -> 列出查询结果客户
	 * 部门管理可查看合作中客户，超级管理可查看所有客户
	 */
	public function getCustomerQueryList($customer,$start,$records) {
		$sql = "SELECT * FROM `s_customer` WHERE (`name` LIKE '%{$customer}%' OR `nickname` LIKE '%{$customer}%') ";
		if ($_SESSION['Login_section'] == 0) {
		} else {
			$sql .= "AND `status` = '1' ";
		}
		if (isset($start)) {
			$sql .= "LIMIT {$start},{$records};";
		} else {
			$sql .= ";";
		}
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 查询 -> 显示客户详细资料
	 * 部门管理可查看合作中客户，超级管理可查看所有客户
	 */
	public function getCustomerMeta($id) {
		$sql = "SELECT * FROM `s_customer` WHERE `id` = '{$id}'";
		if ($_SESSION['Login_section'] == 0) {
			$sql .= ";";
		} else {
			$sql .= " AND `status` = '1';";
		}
		$query = $this -> db -> query($sql);
		return $query -> fetch_assoc();
	}
	/**
	 * 查询 -> 列出设备
	 * 
	 */
	public function getDeviceList($start = -1,$records = 30) {
		$sql = "SELECT * from `s_device`";
		if ($start >= 0) {
			$sql .= " LIMIT {$start},{$records};";
		} else {
			$sql .= ";";
		}
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 查询 -> 列出查询结果设备
	 * 
	 */
	public function getDeviceQueryList($keyword,$start = -1,$records = -1) {
		$sql = "SELECT * FROM `s_device` WHERE (`name` LIKE '%{$keyword}%' OR `mid` LIKE '%{$keyword}%' OR `sn` LIKE '%{$keyword}%')";
		if ($start >= 0 && $records >= 0) {
			$sql .= "LIMIT {$start},{$records};";
		} else {
			$sql .= ";";
		}
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 查询 -> 显示设备详细资料
	 * 
	 */
	public function getDeviceMeta($id) {
		$sql = "SELECT * FROM `s_device` WHERE (`id` = '{$id}' AND `stale` = '1');";
		$query = $this -> db -> query($sql);
		return $query -> fetch_assoc();
	}
	
	/**
	 * 查询 -> 提取当前账号最后一条记录
	 */
	public function getWorkrecord() {
		$sql = "SELECT * from `s_check` WHERE `account` = '{$_SESSION['Login_account']}' ORDER BY `date` desc LIMIT 0,1;";
		$query = $this -> db -> query($sql);
		$num_rows = $query -> num_rows;
		if ($num_rows == 1) {
			return $query -> fetch_assoc();
		} else {
			return FALSE;
		}
	}
	/**
	 * 查询 -> 列出近2个月值班记录
	 */
	public function getWorkrecordList() {
		$curmonth = date("Y-m");
		$premonth = date("Y-m",strtotime($curmonth)-1); 
		$sql = "SELECT * from `s_check` WHERE `account` = '{$_SESSION['Login_account']}' AND (`date` LIKE '{$curmonth}%' OR `date` LIKE '{$premonth}%');";
		$query = $this -> db -> query($sql);
		return $query;
	}
	
	/**
	 * 查询 -> 列出服务报告单
	 */
	public function getServicesList($start = -1,$records = 30) {
		$sql = "SELECT * from `view_srvform` ORDER BY `stime` desc ";
		if ($start >= 0) {
			$sql .= "LIMIT {$start},{$records};";
		} else {
			$sql .= ";";
		}
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 查询 -> 列出查询结果服务报告单 -- 工程师
	 * 
	 */
	public function getServicesQueryList($engineer,$start = -1,$records = -1) {
		$sql = "SELECT * FROM `view_srvform` WHERE (`mainmbr` LIKE '%{$engineer}%' OR `submbr` LIKE '%{$engineer}%') ORDER BY `stime` desc ";
		if ($start >= 0 && $records >= 0) {
			$sql .= "LIMIT {$start},{$records};";
		} else {
			$sql .= ";";
		}
		$query = $this -> db -> query($sql);
		return $query;
	}

	

	/**
	 * 事务操作 -> 注册账号{更新密码，清除授权码，加入授权表}
	 */
	public function registStaff($id, $account, $passwd) {
		$sql_updatepasswd = "UPDATE `s_staff` SET `passwd` = '{$passwd}',`authorizer` = '',`status` = '1' WHERE `id` = '{$id}';";
		$sql_insertauthority = "INSERT INTO `s_authority` (`account`,`authority`) values ('{$account}','TMGR');";
		$sql_insertworktime = "INSERT INTO `s_worktime` (`account`) values ('{$account}');";
		$sql_result = $this -> transationSQL($sql_updatepasswd, $sql_insertauthority, $sql_insertworktime);
		if ($sql_result == "commit") {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * 删除 -> 审核新账号未通过
	 */
	public function deleteNoStaff($id) {
		$sql = "DELETE from `view_unLoginAuth`  WHERE `id` = '{$id}';";
		$query = $this -> db -> query($sql);
		return $query;
	}
}
?>