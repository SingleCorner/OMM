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
		return $query -> fetch_assoc();
	}
	/**
	 * 查询 -> 单条件数据
	 */
	public function getTableAllWhere($value_table, $field, $field_value, $start = -1, $count = -1) {
		$sql="SELECT * FROM `{$value_table}` WHERE `{$field}` = '{$field_value}'";
		if ($start >= 0 && $count > 0) {
			$sql .= " LIMIT {$start},{$count}";
		}
		$sql .= ";";
		$query = $this -> db -> query($sql);
		return $query -> fetch_assoc();
	}
	/**
	 * 更新 -> 单列数据
	 */
	public function updateTable($value_table, $field, $field_value, $task_id) {
		$sql = "UPDATE `{$value_table}` SET `{$field}` = '{$field_value}' WHERE `t_id` = '{$task_id}';";
		$query = $this -> db -> query($sql);
		return $query -> fetch_assoc();
	}
	/**
	 * 查询影响数据
	 */
	public function affected() {
		return $this -> db -> affected_rows;
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
	

	//更新 -> 修改用户密码
	public function updateLoginPasswd($newpasswd, $account) {
		$sql = "UPDATE `view_LoginAuth` SET `passwd` = '{$newpasswd}' WHERE `account` = '{$account}';";
		return $this -> db -> query($sql);
	}

	
	//事务处理
	public function transationSQL() {
		$this -> db -> autocommit(false);
		$sql = func_get_args();
		foreach ($sql as $query) {
			$result = $this -> db -> query($sql);
			if (!$result) {
				$check = "1";
			}
		}
		if ($check == "1") {
			$this -> db -> rollback();
		} else {
			$this -> db -> commit();
		}
	}
}
?>