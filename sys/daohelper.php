<?php
/**
 * dao帮助类
 * @package sys
 * 
 */

class sys_daohelper {
	public static $defaultPDO = array();
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
	public function __construct($className, $tableName = null, $conf = array())
	{
		$this->pdo = self::getPdo($conf);
	
		$this->className = $className;
		if ($className && empty($tableName)) {
			$ref = new ReflectionClass($className);
			$this->tableName = $ref->getConstant('TABLE_NAME');
		} else {
			$this->tableName = $tableName;
		}
	}

	public static function getPdo($conf)
	{
		$db_name = $conf['dbn'];
		if (! isset(self::$defaultPDO[$db_name]) || !is_object(self::$defaultPDO[$db_name])) {
			self::$defaultPDO[$db_name] = new PDO('mysql:host=' . $conf['host'] . ';port=' . $conf['port'] . ';dbname=' . $conf['dbn'],$conf['user'],$conf['pass'],array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES 'UTF8';",PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));//PDO::ATTR_PERSISTENT => true //持久链接
		}
		
		return self::$defaultPDO[$db_name];
	}
	

	public static function updateFieldMap($field)
	{
		return '`' . $field . '`=:' . $field;
	}


	public static function changeFieldMap($field)
	{
		return '`' . $field . '`=`' . $field . '`+:' . $field;
	}

	public function getLastId()
	{
		return $this->pdo->lastInsertId();
	}

	/**
	 * 添加一个对象到数据库
	 * @param Object $object 对象
	 * @param array $fields 对象的属性数组
	 * @param string $onDuplicate 主键或唯一键冲突时执行的更新语句
	 * @param int $returnType 0 返回影响行数  1返回自增ID
	 * @return int 
	 */
	public function add($fields, $params, $onDuplicate = null, $returnType = 0)
	{
		$strFields = '`' . implode('`,`',$fields) . '`';
		$strValues = ':' . implode(', :',$fields);
		
		$query = 'INSERT INTO `' . $this->tableName . '`(' . $strFields . ') VALUES (' . $strValues . ')';

		if ($onDuplicate != null)
			$query .= 'ON DUPLICATE KEY UPDATE ' . $onDuplicate;
		
		$statement = $this->pdo->prepare($query);
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
		
		if($returnType==0){
			return $statement->rowCount();
		}else{
			return $this->pdo->lastInsertId();
		}
	}
	
	/**
	 * 更新所有符合条件的对象
	 *
	 * @param array $fields
	 * @param array $params
	 * @param string $where
	 */
	public function update($fields, $params, $where, $change = false)
	{
		if ($change) {
			$updateFields = array_map(__CLASS__ . '::changeFieldMap',$fields);
		} else {
			$updateFields = array_map(__CLASS__ . '::updateFieldMap',$fields);
		}
		
		$strUpdateFields = implode(',',$updateFields);
		$query = 'UPDATE `' . $this->tableName . '` SET ' . $strUpdateFields . ' WHERE ' . $where;

		$statement = $this->pdo->prepare($query);
		if ($this->className) {
			$object = $params;
			$params = array();
			foreach($fields as $field) $params[$field] = $object->$field;
		}

		//print_r($params);
		$ret = $statement->execute($params);
		if (!$ret) {
			$this->writeErrorLog($query, $statement, $params);
		}		
		$this->writeLog($query, $params);
		
		//if (!$ret) return false;
		//return true;
		$rowCount = $statement->rowCount();
		//记录重复更新日志
		if($rowCount==0) $this->writeDuplicateLog($query, $params);
		return $rowCount;
	}


	public function fetchSingle($where = '1', $params = array(), $fields = '*', $orderBy = null)
	{
		$query = "SELECT " . $fields . " FROM `" . $this->tableName . "` WHERE " . $where;
		
		if ($orderBy) {
			$query .= " order by " . $orderBy;
		}
		
		$query .= " limit 1";
		$this->query = $query;
		//sys_utils::dump($query);
		$statement = $this->pdo->prepare($query);
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
		
		$ret = $statement->fetch();		
		$statement->closeCursor();
		return $ret;
	}
	
	/**
	 * 取得所有符合条件的对象
	 *
	 * @param string $where sql条件
	 * @param array $params sql参数
	 * @param string $fields sql字段
	 * @return array 对象数组
	 */
	public function fetchAll($where = '1', $params = array(), $fields = '*', $orderBy = null, $limit = null)
	{
		$query = "SELECT " . $fields . " FROM `" . $this->tableName . "` WHERE " . $where;
		
		if ($orderBy) {
			$query .= " order by " . $orderBy;
		}
		
		if ($limit) {
			$query .= " limit " . $limit;
		}

		$statement = $this->pdo->prepare($query);
		
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
		$ret = $statement->fetchAll();
		$statement->closeCursor();
		return $ret;
	}


	/**
	 * 删除符合条件的记录
	 *
	 * @param string $where
	 * @param array $params
	 */
	public function remove($where, $params)
	{
		$where = trim($where);
		if (empty($where))
			return;
		
		$query = "DELETE FROM `" . $this->tableName . "` WHERE " . $where;
		
		$statement = $this->pdo->prepare($query);
		$ret = $statement->execute($params);
		if (!$ret) {
			$this->writeErrorLog($query, $statement, $params);
		}
		$this->writeLog($query, $params);
	
		//if (!$ret) return false;
		//return true;
		$ret = $statement->rowCount();		
		$statement->closeCursor();
		return $ret;
	}


	public function execBySql($query, $params = array())
	{
		$this->query = $query;
		$statement = $this->pdo->prepare($query);
		$ret = $statement->execute($params);
		if (!$ret) {
			$this->writeErrorLog($query, $statement, array());
		}
		$this->writeLog($query, array());
		
		//if (!$ret) return false;
		//return true;
		$ret = $statement->rowCount();
		$statement->closeCursor();
		return $ret;
	}

	public function InsertBulk($query,$rows)
	{
		$this->query = $query;
		$dbh = $this->pdo;
		$dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
		$dbh->setAttribute(PDO::ATTR_AUTOCOMMIT,false);
		$succeed = false;
		try {
			
			$dbh->beginTransaction();

			$row_count = count($rows);
			$succeed_count = 0;

			$stmt = $this->pdo->prepare($query);
			foreach ($rows as $params)
			{	
				if($stmt->execute($params))	{
					$succeed_count++;
				}
			}

			if($row_count == $succeed_count)
			{
				$dbh->commit();
				$succeed = true;
			}
			else
			{
				$dbh->rollBack();
			}
		}
		catch(Exception $e)	{	
			sys_log::getLogger()->fatal( sys_log::format('InsertBulk',array('Message'=>$e->getMessage() ,'time'=>time()) ));			
			$dbh->rollBack();		
		}
		$dbh->setAttribute(PDO::ATTR_AUTOCOMMIT,true);
		return $succeed;
	}


	public function UpdateBulk($rows)
	{
		$fields = array();
		$data = array();		
		$i = 0;
		foreach ($rows as $index => $row) {		
			if($i == 0){
				$fields = array_keys($row);
			}
			$data[$index] = '('.$this->array_quote($row , $fields).')';
			$i++;
		}
		$sqlPlus = implode(',' , $data);
		$strFields = implode(',',$fields);
		$KEY_UPDATE = '';
		$fieldsCount = count($fields);
		for ($i=0; $i < $fieldsCount; $i++) { 
			if($i == $fieldsCount - 1){
				$KEY_UPDATE .= $fields[$i].'=VALUES('.$fields[$i].');';
			} else {
				$KEY_UPDATE .= $fields[$i].'=VALUES('.$fields[$i].'), ';
			}	
		}
		$query = 'INSERT INTO `' . $this->tableName . '`(' . $strFields . ') VALUES '.$sqlPlus .' ON DUPLICATE KEY UPDATE '.$KEY_UPDATE;
		return $this->execBySql($query);
	}

	
	/**
	 * 直接执行SQL语句
	 *
	 * @param string $sql
	 * @return array
	 */
	public function fetchBySql($query, $params = array())
	{
		$this->query = $query;
		$statement = $this->pdo->prepare($query);
		$ret = $statement->execute($params);
		if (!$ret) {
			$this->writeErrorLog($query, $statement, array());
		}
		$this->writeLog($query, array());
		
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$ret = $statement->fetchAll();		
		$statement->closeCursor();
		return $ret;
	}
	
	
	/**
	 * 直接执行SQL语句
	 *
	 * @param string $sql
	 * @return array
	 */
	public function fetchByQuery($query, $params = array())
	{
		$this->query = $query;
		$statement = $this->pdo->query($query);	
		if (!$statement) {
			$this->writeErrorLog($query, $statement, array());
		}
		$this->writeLog($query, array());	
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$row = $statement->fetchAll();
		
		$statement->closeCursor();
		return $row;
	}


	private function writeErrorLog($query, $statement, $params)
	{
		$errinfo = $statement->errorInfo();
		if (sys_define::DEBUG_MODE) {
			$errary = array_merge(array('code'=>$statement->errorCode(),'info'=>$errinfo[2],'query'=>$query), $params);
			dump($errary);
			exit;
		}

		sys_log::getLogger()->fatal( sys_log::format('sqlError', array_merge(array('code'=>$statement->errorCode(),'info'=>$errinfo[2],'query'=>$query), $params)));
	}
	
	private function writeDuplicateLog($query, $params)
	{
		$params = $params ? $params : array();
		sys_log::getLogger()->debug( sys_log::format('duplicateLog', array_merge(array('query'=>$query), $params) ));		
	}
	
	private function writeLog($query, $params)
	{
		$params = $params ? $params : array();
		if (sys_define::LOG_QUERY) {
			sys_log::getLogger()->debug( sys_log::format('query', array_merge(array('query'=>$query), $params) ));
		}
	}

	private function array_quote($row,$fields)
    {
        $rowList = array();
        foreach ($fields as $key => $field) {         	
        	if($row[$field] == null){
        		$rowList[] = "''";
        	} else {
        		$rowList[] = is_string($row[$field]) ? "'{$row[$field]}'" : $row[$field];
        	}         	
        }         
        return implode($rowList, ',');
    }
	
}
