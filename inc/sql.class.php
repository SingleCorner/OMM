<?php
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
	 * 获取表的全部数据
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
	
	public function getTableAllWhere($value_table, $field, $field_value, $start = -1, $count = -1) {
		$sql="SELECT * FROM `{$value_table}` WHERE `{$field}` = '{$field_value}'";
		if ($start >= 0 && $count > 0) {
			$sql .= " LIMIT {$start},{$count}";
		}
		$sql .= ";";
		$query = $this -> db -> query($sql);
		return $query -> fetch_assoc();
	}
	
	public function getTableAllWhere2($value_table, $field, $field_value, $field_2, $field_value_2, $start = -1, $count = -1) {
		$sql="SELECT * FROM `{$value_table}` WHERE `{$field}` = '{$field_value}' AND `{$field_2}` = '{$field_value_2}'";
		if ($start >= 0 && $count > 0) {
			$sql .= " LIMIT {$start},{$count}";
		}
		$sql .= ";";
		return $this -> db -> query($sql);
	}
	
	public function getTableAllAsc($value_table, $field, $start = -1, $count = -1) {
		$sql="SELECT * FROM `{$value_table}` ORDER BY `{$field}` ASC";
		if ($start >= 0 && $count > 0) {
			$sql .= " LIMIT {$start},{$count}";
		}
		$sql .= ";";
		return $this -> db -> query($sql);
	}
	
	public function getTableAllDesc($value_table, $field, $start = -1, $count = -1) {
		$sql="SELECT * FROM `{$value_table}` ORDER BY `{$field}` DESC";
		if ($start >= 0 && $count > 0) {
			$sql .= " LIMIT {$start},{$count}";
		}
		$sql .= ";";
		return $this -> db -> query($sql);
	}
	
	public function userDefine($sql){
		return $this -> db -> query($sql);
	}
	
	//修改数据
	public function updateTable($value_table, $field, $field_value, $task_id) {
		$sql = "UPDATE `{$value_table}` SET `{$field}` = '{$field_value}' WHERE `t_id` = '{$task_id}';";
		return $this -> db -> query($sql);
	}
	
	//插入数据
	public function insertTable($value_table, $field, $field_value) {
		$sql = "INSERT INTO `{$value_table}` ($field) VALUES ($field_value)";
		return $this -> db -> query($sql);
	}
	
	public function fetch($query) {
		return $query -> fetch_assoc();
	}
	
	public function rows($query){
		return $query -> num_rows;
	}
	
	public function close() {
		return $this -> db -> close();
	}
}
?>