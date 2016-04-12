<?php
class QFrameDB
{
	private static $_container      = array();
	private static $_default_config = array(
	"driver"=>"mysql",
	"host"=>"127.0.0.1",
	"port"=>"3306",
	"username"=>"root",
	"password"=>"",
	"charset"=>"gbk",
	"database"=>"test",
	"persistent"=>true,
	"unix_socket"=>"",
	"options"=>array()
	);

	public static function getInstance($config = array())
	{/*{{{*/
		$key = md5(serialize($config));

		if(!isset(self::$_container[$key]) || !(self::$_container[$key] instanceof QFrameDBPDO)) {
			$final_config = array();
			foreach(self::$_default_config as $index=>$value) {
				$final_config[$index] = isset($config[$index]) && !empty($config[$index]) ? $config[$index] : self::$_default_config[$index];
			}
			self::$_container[$key] = new QFrameDBPDO($final_config);
		}

		return self::$_container[$key];
	}/*}}}*/

    public static function destroyInstance($config=array())
    {/*{{{*/
         $key = md5(serialize($config)); 
         if(isset(self::$_container[$key]) && (self::$_container[$key] instanceof QFrameDBPDO))
         {
               self::$_container[$key]->close();
               unset(self::$_container[$key]);
         }
         return true;
    }/*}}}*/
}

class QFrameDBPDO
{
	private $_config         = array();
	private $_conn           = null;
	private $_fetch_type     = PDO::FETCH_ASSOC;
	private $_debug          = false;
	private $_log            = null;
	private $_optimize       = false;
	private $_transaction    = false;
	private $_error_mode     = PDO::ERRMODE_EXCEPTION;
	private $_reconnected    = false; //是否需要重新链接
	private $_auto_reconnect = true;  //是否需要开启自动重连

	public function __construct($config)
	{/*{{{*/
		$this->_config = $config;
	}/*}}}*/

	private function _connect()
	{/*{{{*/
		if($this->_conn == null) {
			if($this->_config["unix_socket"]) {
				$dsn = "mysql:dbname={$this->_config["database"]};unix_socket={$this->_config["unix_socket"]}";
			} else {
				$dsn = "{$this->_config["driver"]}:dbname={$this->_config["database"]};host={$this->_config["host"]};port={$this->_config["port"]}";
			}

			$username   = $this->_config["username"];
			$password   = $this->_config["password"];
			$options    = array_unique(array_merge(array(PDO::ATTR_PERSISTENT=>$this->_config["persistent"]), $this->_config["options"]));

			try {
				$this->_conn = new PDO($dsn, $username, $password, $options);
			} catch (PDOException $e) {
				throw new QFrameDBException($e->getMessage(), $e->getCode());
			}

			$this->_conn->setAttribute(PDO::ATTR_ERRMODE, $this->_error_mode);
			$this->execute("SET NAMES '{$this->_config["charset"]}'");
			$this->execute("SET character_set_client=binary");
		}
	}/*}}}*/

	private function _exec($sql, $params)
	{/*{{{*/
		$this->_connect();

		if($this->_debug) {
			print $this->getBindedSql($sql, $params)."\n";
		}

		$stmt = new QFrameDBStatment($this->_conn->prepare($sql));
		if(is_array($params)) {
			if(!empty($params)) {
				$i = 0;
				foreach($params as $value) {
					$stmt->bind(++$i, $value);
				}
			}
		} else {
			$stmt->bind(1, $params);
		}
		$execute_return = $stmt->execute();

		if($this->_optimize && preg_match("/^select/i", $sql)) {
			$fetch_mode = $this->_fetch_type;
			$this->setFetchMode(PDO::FETCH_ASSOC);
			$debug = $this->_debug;
			$this->setDebug(true);
			QFrameDBExplainResult::draw($this->getAll("explain ".$this->getBindedSql($sql, $params)));
			$this->setDebug($debug);
			$this->setFetchMode($fetch_mode);
		}

		return array("stmt"=>$stmt, "execute_return"=>$execute_return);
	}/*}}}*/

	private function _process($sql, $params)
	{/*{{{*/
		//关闭直接事务语句
		if(in_array(preg_replace("/\s{2,}/", " ", strtolower($sql)), array("begin", "commit", "rollback", "start transaction", "set autocommit=0", "set autocommit=1"))) {
			throw new QFrameDBException("为避免操作异常，请使用包装后的事务处理接口[startTrans, commit, rollback]");
		}

		if($this->_transaction) {
			if($this->_reconnected) {
				throw new QFrameDBException("数据库链接已丢失!");
			} else {
				try {
					$arr_exec_result = $this->_exec($sql, $params);
				} catch (PDOException $e) {
					if(in_array($e->errorInfo[1], array(2013, 2006))) {
						$this->_reconnected = true;
					}

					throw new QFrameDBException($e->errorInfo[2], $e->errorInfo[1]);
				}
			}
		} else {
			try {
				$arr_exec_result = $this->_exec($sql, $params);
			} catch (PDOException $e) {
				if($this->_auto_reconnect && in_array($e->errorInfo[1], array(2013, 2006))) {
					try {
                        $this->close();
						$arr_exec_result = $this->_exec($sql, $params);
						$this->_reconnected = true;
					} catch (PDOException $e) {
						throw new QFrameDBException($e->errorInfo[2], $e->errorInfo[1]);
					}
				} else {
					throw new QFrameDBException($e->errorInfo[2], $e->errorInfo[1]);
				}
			}
		}

		return $arr_exec_result;
	}/*}}}*/

	private function _checkSafe($sql, $is_open_safe = true)
	{/*{{{*/
		if(!$is_open_safe) {
			return true;
		}

		$string  = strtolower($sql);
		$operate = strtolower(substr($sql, 0, 6));
		$is_safe = true;
		switch ($operate) {
			case "select":
				if(strpos($string, "where") && !preg_match("/\(.*\)/", $string) && !strpos($string, "?")) {
					$is_safe = false;
				}
				break;
			case "insert":
			case "update":
			case "delete":
				if(!strpos($string, "?")) {
					$is_safe = false;
				}
				break;
		}

		if(!$is_safe) {
			throw new QFrameDBException("SQL语句:[$sql],存在SQL注入漏洞隐患，请改用bind方式处理或关闭sql执行safe模式.");
		}

		return $is_safe;
	}/*}}}*/

	public function getInsertId()
	{/*{{{*/
		return $this->_conn->lastInsertId();
	}/*}}}*/

	public function execute($sql, $params = array(), $is_open_safe = true)
	{/*{{{*/
		$this->_checkSafe($sql, $is_open_safe);

		$arr_process_result = $this->_process($sql, $params);
       
		if($arr_process_result["execute_return"]) {
			$operate = strtolower(substr($sql, 0, 6));
			switch ($operate) {
				case "insert":
					$arr_process_result["execute_return"] = $this->getInsertId();
					break;
				case "update":
				case "delete":
					$arr_process_result["execute_return"] = $arr_process_result["stmt"]->getEffectedRows();
					break;
				default:
					break;
			}
		}
  
		if($this->_log != null) {
			$this->_log->sql($sql, $params);
		}

		return $arr_process_result["execute_return"];
	}/*}}}*/

	public function query($sql, $params = array(), $is_open_safe = true)
	{/*{{{*/
		$this->_checkSafe($sql, $is_open_safe);
		$result = $this->_process($sql, $params);
		return $result["stmt"];
	}/*}}}*/

	public function getOne($sql, $params = array(), $safe = true)
	{/*{{{*/
		$stmt   = $this->query($sql, $params, $safe);
		$record = $stmt->fetch($this->_fetch_type);
		return is_array($record) && !empty($record) ? array_shift($record) : null;
	}/*}}}*/

	public function getRow($sql, $params = array(), $safe = true)
	{/*{{{*/
		$stmt   = $this->query($sql, $params, $safe);
		$record = $stmt->fetch($this->_fetch_type);
		return is_array($record) && !empty($record) ? $record : array();
	}/*}}}*/

	public function getAll($sql, $params = array(), $safe = true)
	{/*{{{*/
		$stmt = $this->query($sql, $params, $safe);
		$data = array();
		while ($record = $stmt->fetch($this->_fetch_type)) {
			$data[] = $record;
		}
		return $data;
	}/*}}}*/

	private function _operate($table, $record, $operate, $condition = "", $params = array())
	{/*{{{*/
		if(in_array($operate, array("insert", "replace", "update"))) {
			$fields = is_array($record) ? array_keys($record)   : array();
			$values = is_array($record) ? array_values($record) : array();

			if(empty($fields)) {
				throw new QFrameDBException("\$record 操作数据必须使用关联数组形式");
			}
		}

		switch ($operate) {
			case "insert":
			case "replace":
				$sql = "$operate into $table (`".implode("`,`", $fields)."`) values (".str_repeat("?,", count($fields) - 1)."?)";
				return $this->execute($sql, $values);
				break;
			case "update":
				$sql = "update $table set ";
				foreach($fields as $field) {
					$sql .= "$field=?,";
				}
				$sql = substr($sql, 0, -1);

				if($condition) {
					$sql .= " where ".$condition;
				}
				is_array($params) ? $values = array_merge($values, $params) : $values[] = $params;
				return $this->execute($sql, $values);
				break;
			case "delete":
				$sql = "delete from $table where $condition";
				return $this->execute($sql, $params);
				break;
		}
		return true;
	}/*}}}*/

	public function insert($table, $record)
	{/*{{{*/
		return $this->_operate($table, $record, "insert");
	}/*}}}*/

	public function replace($table, $record)
	{/*{{{*/
		return $this->_operate($table, $record, "replace");
	}/*}}}*/

	public function update($table, $record, $condition, $params)
	{/*{{{*/
        try {
            return $this->_operate($table, $record, "update", $condition, $params);
        } catch (QFrameDBException $e) {
            throw new QFrameDBException($e->getMessage());
        }
	}/*}}}*/

	public function delete($table, $condition, $params)
	{/*{{{*/
		return $this->_operate($table, null, "delete", $condition, $params);
	}/*}}}*/

	public function setWaitTimeOut($seconds)
	{/*{{{*/
		$this->execute("set wait_timeout=$seconds");
	}/*}}}*/

	public function setAutoReconnect($flag)
	{/*{{{*/
		$this->_auto_reconnect = $flag;
	}/*}}}*/

	public function setDebug($flag = false)
	{/*{{{*/
		$this->_debug = $flag;
	}/*}}}*/

	public function setOptimize($flag = false)
	{/*{{{*/
		$this->_optimize = $flag;
	}/*}}}*/

	public function setFetchMode($fetch_type = PDO:: FETCH_ASSOC)
	{/*{{{*/
		$this->_fetch_type = $fetch_type;
	}/*}}}*/

	public function setLog($log)
	{/*{{{*/
		$this->_log = $log;
	}/*}}}*/

	public function startTrans()
	{/*{{{*/
		if($this->_transaction) {
			throw new QFrameDBException("之前开启的事务尚未结束，事务处理不能嵌套操作!");
		}

        $this->_connect();

		try {
			$this->_conn->beginTransaction();
		} catch (PDOException $e) {
			$errorInfo = $this->_conn->errorInfo();
			throw new QFrameDBException($errorInfo[2], $errorInfo[1]);
		}

		$this->_transaction = true;
		$this->_reconnected = false;
	}/*}}}*/

	public function commit()
	{/*{{{*/
		if(!$this->_transaction) {
			throw new QFrameDBException("之前开启的事务已经被提交或没有开启，请仔细查看事务处理过程中的操作语句!");
		}

		$this->_transaction = false;
		$this->_reconnected = false;

		try {
			$this->_conn->commit();
		} catch (PDOException $e) {
			$errorInfo = $this->_conn->errorInfo();
			throw new QFrameDBException($errorInfo[2], $errorInfo[1]);
		}
	}/*}}}*/

	public function rollback()
	{/*{{{*/
		if(!$this->_transaction) {
			throw new QFrameDBException("之前开启的事务已经被提交或没有开启，请仔细查看事务处理过程中的操作语句!");
		}

		$this->_transaction = false;
		$this->_reconnected = false;

		try {
			$this->_conn->rollback();
		} catch (PDOException $e) {
			$errorInfo = $this->_conn->errorInfo();
			throw new QFrameDBException($errorInfo[2], $errorInfo[1]);
		}
	}/*}}}*/

	public function close()
	{/*{{{*/
		$this->_conn = null;
	}/*}}}*/

	public function getBindedSql($sql, $params = array())
	{/*{{{*/
		if(!preg_match("/\?/", $sql)) {
			return $sql;
		}
		
		/* 先找出非正常的变量区域并用"#"代替 */
		preg_match_all('/(?<!\\\\)\'.*(?<!\\\\)\'/U', $sql, $arr_match_list);
		$arr_exists_list = $arr_match_list[0];
		foreach($arr_match_list[0] as $value) {
			$sql = str_replace($value, "#", $sql);
		}
		
		if(!is_array($params)) {
			$params = array($params);
		}
		
		/* 根据#或?分解语句,将内容填充到对应位置上 */
		preg_match_all("/[#\?]/", $sql, $arr_match_list);
		$arr_split_list = preg_split("/[#\?]/", $sql);

		$sql = "";
		foreach($arr_match_list[0] as $key=>$flag) {
			$sql .= $arr_split_list[$key].($flag == "#" ? array_shift($arr_exists_list) : $this->quote(array_shift($params)));
		}

		return $sql;
	}/*}}}*/

	public function quote($string)
	{/*{{{*/
		return $this->_conn->quote($string);
	}/*}}}*/
}

class QFrameDBStatment
{
	private $_stmt;

	public function __construct($stmt)
	{/*{{{*/
		$this->_stmt = $stmt;
	}/*}}}*/

	public function fetch($mode = PDO::FETCH_ASSOC)
	{/*{{{*/
		return $this->_stmt->fetch($mode);
	}/*}}}*/

	public function execute()
	{/*{{{*/
		return $this->_stmt->execute();
	}/*}}}*/

	public function bind($parameter, $value)
	{/*{{{*/
		return $this->_stmt->bindValue($parameter, $value);
	}/*}}}*/

	public function getEffectedRows()
	{/*{{{*/
		return $this->_stmt->rowCount();
	}/*}}}*/
}

class QFrameDBException extends Exception
{
	public function __construct($message,$code=0)
	{/*{{{*/
		$message = "数据库 错误[$code]:$message ($code)";

		parent::__construct($message,$code);
	}/*}}}*/
}
?>
