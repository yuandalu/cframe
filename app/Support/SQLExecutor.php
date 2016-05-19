<?php

namespace App\Support;

class SQLExecutor
{
    const CONN_LONG  = true;
    const CONN_SHORT = false;

    private $_dbh = null;
    private $_log = null;
    protected $transactions = 0;

    public function __construct($dbconf, $ctype = '', $charset = 'UTF8MB4')
    {
        if ('' == $ctype) {
            $ctype = self::CONN_SHORT;
        }
        $this->_dbh = new \PDO('mysql:host='.$dbconf['host'].';port='.$dbconf['port'].';dbname='.$dbconf['name'], $dbconf['user'], $dbconf['pass'],
            array(\PDO::ATTR_PERSISTENT => $ctype));
        $this->_dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->_dbh->query('SET NAMES '.$charset);
    }

    public function regLogObj($obj)
    {
        $this->_log = $obj;
    }

    public function query($sql, $values = array())
    {
        //$this->logSql($sql,$values);

        $i   = 0;
        $sth = $this->_dbh->prepare($sql);

        if (!empty($values)) {
            foreach ($values as $value) {
                $sth->bindValue(++$i, $value);
            }
        }

        if($sth->execute())
        {
            $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
            $this->logSql($sql, $values, "Memory:".memory_get_usage());
            if (is_array($result) && array_key_exists(0, $result)) {
                return $result[0];
            }
        }

        return null;
    }

    public function querys($sql, $values = array())
    {
        //$this->logSql($sql,$values);

        $i   = 0;
        $sth = $this->_dbh->prepare($sql);
        if (!empty($values)) {
            foreach ($values as $value) {
                $sth->bindValue(++$i, $value);
            }
        }

        if ($sth->execute()) {
            $this->logSql($sql, $values, "Memory:".memory_get_usage());
            return $sth->fetchAll(\PDO::FETCH_ASSOC);

        }
        return array();
    }

    private function formatValues($values)
    {
        $result = array();
        foreach ($values as $k => $v) {
            if (is_string($v)) {
                $result[$k] = "'".$v."'";
                continue;
            }
            if (is_null($v)) {
                $result[$k] = 'null';
            }
            $result[$k] = $v;
        }
        return $result;
    }

    private function logSql($sql, $values = array(), $add='')
    {
        if (is_null($this->_log)) {
            return;
        }

        if (empty($values)) {
            $this->_log->log($sql);
            return;
        }
        $backtrace = debug_backtrace();
        $trace = '';
        for ($i = 0; $i < 4; $i++) {
            if (empty($backtrace[$i])) {
                break;
            }
            $trace.= "\n".$backtrace[$i]['class'].'.'.$backtrace[$i]['function'].' '.$backtrace[$i]['line'];
        }


        $str = str_replace('%', '{#}', $sql);
        $str = vsprintf(str_replace('?', '%s', $str), $this->formatValues($values));
        $str = str_replace('{#}', '%', $str);
        $request_uri = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';
        $this->_log->log($str . $trace . ($add==''?'':"\n".$add."\n".$request_uri));
    }

    public function exeNoQuery($sql, $values = array())
    {
        if (env('READONLY_MODE', 'local')) {
            return false;
        }

        $this->logSql($sql, $values);

        $i   = 0;
        $sth = $this->_dbh->prepare($sql);
        foreach ($values as $value) {
            $sth->bindValue(++$i, $value);
        }
        if (!$sth->execute()) {
            return false;
        }
        return $sth->rowCount();
    }

    public function execute($sql, $values = array())
    {

        $this->logSql($sql, $values);

        $i   = 0;
        $sth = $this->_dbh->prepare($sql);
        foreach ($values as $value)
        {
            $sth->bindValue(++$i, $value);
        }
        return $sth->execute();
    }

    public function beginTrans()
    {
        ++$this->transactions;
        if ($this->transactions == 1) {
            $this->_dbh->beginTransaction();
        }
    }

    public function commit()
    {
        if (env('READONLY_MODE', 'local')) {
            $this->_dbh->rollback();
            ErrorSvc::show(ErrorSvc::ERR_BUSY);
            exit();
        }
        if ($this->transactions == 1) {
            return $this->_dbh->commit();
        }
        --$this->transactions;
    }

    public function rollback()
    {
        if ($this->transactions == 1) {
            return $this->_dbh->rollback();
        }
        $this->transactions = max(0, $this->transactions - 1);
    }

    public function transactionLevel()
    {
        return $this->transactions;
    }

    public function getLastInsertID()
    {
        return (int) $this->_dbh->lastInsertId();
    }
}