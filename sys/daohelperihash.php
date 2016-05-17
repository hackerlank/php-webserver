<?php
/**
 * dao帮助类
 * @package sys
 * 
 */

class sys_daohelperihash {
	private static $defaultPDO = array();
	private static $dbuse = -1;
	/**
	 * pdo对象
	 *
	 * @var PDO
	 */
	public $pdo;
	/**
	 * 数据表名
	 *
	 * @var string
	 */
	public $tableName;
	/**
	 * 类名
	 *
	 * @var string
	 */
	public $className;
	
	public $query;


	/**
	 * 构造函数
	 *
	 * @param PDO $pdo
	 * @param string $tableName
	 * @param string $className
	 */
	public function __construct($className, $tableName = null)
	{
		//$this->pdo = self::getInstance(); // 实际使用时创建
		$this->className = $className;
		if ($className && empty($tableName)) {
			$ref = new ReflectionClass($className);
			$this->tableName = $ref->getConstant('TABLE_NAME');
		} else {
			$this->tableName = $tableName;
		}
	}

	private function getHashConfig($id)
	{
		$id = (int)$id;
		$dbid = $id % sys_define::HASH_MAX;
		
		$serverid = $dbid % count(sys_define::$hash_db);
		$conf = sys_define::$hash_db[$serverid];
		$conf['dbn'] = $conf['dbn'] . sys_define::$sites[CURR_SITE]['prefix'] . '_hash' . $dbid;
		$conf['dbid'] = $dbid;
		$conf['serverid'] = $serverid;
		return $conf;
	}
	
	private function getPdo($id)
	{
		$conf = $this->getHashConfig($id);
		$serverid = $conf['serverid'];
		if (isset(self::$defaultPDO[$serverid]) && is_object(self::$defaultPDO[$serverid])) { // 已连接
			if (self::$dbuse != $conf['dbid']) { // 要切换当前库
				$query = 'use '.$conf['dbn'];
				$statement = self::$defaultPDO[$serverid]->prepare($query);
				$statement->execute();
				$this->writeLog($query, array());
				self::$dbuse = $conf['dbid'];
			}
			return self::$defaultPDO[$serverid];
		}

		self::$defaultPDO[$serverid] = new PDO('mysql:host=' . $conf['host'] . ';port=' . $conf['port'] . ';dbname=' . $conf['dbn'], $conf['user'], $conf['pass'], array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES 'UTF8';",PDO::ATTR_ERRMODE=>PDO::ERRMODE_SILENT));
		$query = 'use '.$conf['dbn'];
		$statement = self::$defaultPDO[$serverid]->prepare($query);
		$statement->execute();
		$this->writeLog($query, array());
		self::$dbuse = $conf['dbid'];
		return self::$defaultPDO[$serverid];
	}
	
	public static function updateFieldMap($field)
	{
		return '`' . $field . '`=:' . $field;
	}


	public static function changeFieldMap($field)
	{
		return '`' . $field . '`=`' . $field . '`+:' . $field;
	}


	public function getLastId($id)
	{
		if (false == ($pdo = $this->getPdo($id))) return false;
		return $pdo->lastInsertId();
	}

	
	/**
	 * 添加一个对象到数据库
	 * @param Object $object 对象
	 * @param array $fields 对象的属性数组
	 * @param string $onDuplicate 主键或唯一键冲突时执行的更新语句
	 * @return int 添加这条记录生成的主键值
	 */
	public function add($id, $fields, $params, $onDuplicate = null)
	{
		if (false == ($pdo = $this->getPdo($id))) return false;
		
		$strFields = '`' . implode('`,`',$fields) . '`';
		$strValues = ':' . implode(', :',$fields);
		
		$query = 'INSERT INTO `' . $this->tableName . '` (' . $strFields . ') VALUES (' . $strValues . ')';
		
		if ($onDuplicate != null)
			$query .= 'ON DUPLICATE KEY UPDATE ' . $onDuplicate;
		
		$statement = $pdo->prepare($query);
		if ($this->className) {
			$object = $params;
			$params = array();
			foreach($fields as $field) $params[$field] = $object->$field;
		}
		$ret = $statement->execute($params);
		if (!$ret) {
			$this->writeErrorLog($query, $statement, $params);
		}
		$this->writeLog($query, $params);
		
		return $pdo->lastInsertId();
	}


	/**
	 * 更新所有符合条件的对象
	 *
	 * @param array $fields
	 * @param array $params
	 * @param string $where
	 */
	public function update($id, $fields, $params, $where, $change = false)
	{
		if (false == ($pdo = $this->getPdo($id))) return false;
		
		if ($change) {
			$updateFields = array_map(__CLASS__ . '::changeFieldMap',$fields);
		} else {
			$updateFields = array_map(__CLASS__ . '::updateFieldMap',$fields);
		}
		
		$strUpdateFields = implode(',',$updateFields);
		$query = 'UPDATE `' . $this->tableName . '` SET ' . $strUpdateFields . ' WHERE ' . $where;
		
		$statement = $pdo->prepare($query);
		if ($this->className) {
			$object = $params;
			$params = array();
			foreach($fields as $field) $params[$field] = $object->$field;
		}
		$ret = $statement->execute($params);
		if (!$ret) {
			$this->writeErrorLog($query, $statement, $params);
		}
		$this->writeLog($query, $params);
		
		//if (!$ret) return false;
		//return true;
		return $statement->rowCount();
	}


	public function fetchSingle($id, $where = '1', $params = array(), $fields = '*', $orderBy = null)
	{
		if (false == ($pdo = $this->getPdo($id))) return false;
		
		$query = "SELECT " . $fields . " FROM `" . $this->tableName . "` WHERE " . $where;
		
		if ($orderBy) {
			$query .= " order by " . $orderBy;
		}
		
		$query .= " limit 1";
		$this->query = $query;
		$statement = $pdo->prepare($query);
		$ret = $statement->execute($params);
		if (!$ret) {
			$this->writeErrorLog($query, $statement, $params);
		}
		$this->writeLog($query, $params);
		
		if ($this->className) {
			$statement->setFetchMode(PDO::FETCH_CLASS,$this->className);
		} else {
			$statement->setFetchMode(PDO::FETCH_ASSOC);
		}
		return $statement->fetch();
	}

	/**
	 * 取得所有符合条件的对象
	 *
	 * @param string $where sql条件
	 * @param array $params sql参数
	 * @param string $fields sql字段
	 * @return array 对象数组
	 */
	public function fetchAll($id, $where = '1', $params = array(), $fields = '*', $orderBy = null, $limit = null)
	{
		if (false == ($pdo = $this->getPdo($id))) return false;
		
		$query = "SELECT " . $fields . " FROM `" . $this->tableName . "` WHERE " . $where;
		
		if ($orderBy) {
			$query .= " order by " . $orderBy;
		}
		
		if ($limit) {
			$query .= " limit " . $limit;
		}
		$statement = $pdo->prepare($query);
		
		$ret = $statement->execute($params);
		if (!$ret) {
			$this->writeErrorLog($query, $statement, $params);
		}
		$this->writeLog($query, $params);
		
		if ($this->className) {
			$statement->setFetchMode(PDO::FETCH_CLASS,$this->className);
		} else {
			$statement->setFetchMode(PDO::FETCH_ASSOC);
		}
		return $statement->fetchAll();
	}


	/**
	 * 删除符合条件的记录
	 *
	 * @param string $where
	 * @param array $params
	 */
	public function remove($id, $where, $params)
	{
		if (false == ($pdo = $this->getPdo($id))) return false;
		
		$where = trim($where);
		if (empty($where))
			return;
		
		$query = "DELETE FROM `" . $this->tableName . "` WHERE " . $where;
		
		$statement = $pdo->prepare($query);
		$ret = $statement->execute($params);
		if (!$ret) {
			$this->writeErrorLog($query, $statement, $params);
		}
		$this->writeLog($query, $params);
	
		//if (!$ret) return false;
		//return true;
		return $statement->rowCount();
	}


	public function execBySql($id, $query)
	{
		if (false == ($pdo = $this->getPdo($id))) return false;
		
		$this->query = $query;
		$statement = $pdo->prepare($query);
		$ret = $statement->execute();
		if (!$ret) {
			$this->writeErrorLog($query, $statement, array());
		}
		$this->writeLog($query, array());
		
		//if (!$ret) return false;
		//return true;
		return $statement->rowCount();
	}
	
	/**
	 * 直接执行SQL语句
	 *
	 * @param string $sql
	 * 
	 * @return array
	 */
	public function fetchBySql($id, $query)
	{
		if (false == ($pdo = $this->getPdo($id))) return false;
		
		$this->query = $query;
		$statement = $pdo->prepare($query);
		$ret = $statement->execute();
		if (!$ret) {
			$this->writeErrorLog($query, $statement, array());
		}
		$this->writeLog($query, array());
		
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		return $statement->fetchAll();
	}


	private function writeErrorLog($query, $statement, $params)
	{
		$errinfo = $statement->errorInfo();
		if (sys_define::DEBUG_MODE) {
			$errary = array_merge(array('code'=>$statement->errorCode(),'info'=>$errinfo[2],'query'=>$query), $params);
			dump($errary);
			exit;
		} else {
			sys_log::getLogger()->fatal( sys_log::format('query', array_merge(array('code'=>$statement->errorCode(),'info'=>$errinfo[2],'query'=>$query), $params) ));			
		}
	}
	
	private function writeLog($query, $params)
	{
		if (sys_define::LOG_QUERY) {
			sys_log::getLogger()->info( sys_log::format('query', array_merge(array('query'=>$query), $params) ));			
		}
	}
	
}
