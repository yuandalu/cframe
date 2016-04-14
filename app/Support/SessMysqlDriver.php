<?php

namespace App\Support;

class SessMysqlDriver
{
    static $HOST = '';
    static $NAME = '';
    static $USER = '';
    static $PASS = '';
    static $PORT = '';
    static $LIFE = '';
    static $CONN = '';

    const TABLE = 'sessions';

    public function __construct($life = '7200') {
        self::$HOST = env('ENV_DB_HOST');
        self::$NAME = env('ENV_DB_NAME');
        self::$USER = env('ENV_DB_USER');
        self::$PASS = env('ENV_DB_PASS');
        self::$PORT = env('ENV_DB_PORT');
        self::$LIFE = $life;
    }

    public function init()
    {
        if (!env('READONLY', 'local')) {
            $handler = new SessHandler();
            session_set_save_handler($handler, true);
        }
    }

    public static function sessOpen()
    {
        if (!self::$CONN = mysql_connect(self::$HOST.':'.self::$PORT, self::$USER, self::$PASS)) {
            die('Could not connect: '.mysql_error());
        }

        if (!mysql_select_db(self::$NAME, self::$CONN)) {
            die('Can\'t use foo : '.mysql_error());
        }

        return true;
    }

    public static function sessClose()
    {
        if (is_resource(self::$CONN)) {
            mysql_close(self::$CONN);
        }
        return true;
    }

    public static function sessRead($skey)
    {
        $sql = "select value ";
        $sql.= "from ".self::TABLE." ";
        $sql.= "where skey = '".$skey."' ";
        $sql.= "and expiry > '".time()."' ";
        $row = mysql_query($sql, self::$CONN);

        if (list($result) = mysql_fetch_row($row)) {
            return $result;
        }
        return false;
    }

    public static function sessWrite($skey, $value)
    {
        $skey   = mysql_real_escape_string($skey);
        $value  = mysql_real_escape_string($value);
        $expiry = time() + self::$LIFE;

        $sql = "insert into ".self::TABLE." ";
        $sql.= "values ('".$skey."', '".$expiry."', '".$value."') ";
        $row = mysql_query($sql, self::$CONN);
        if ($row) {
            return $row;
        }

        $sql = "update ".self::TABLE." set ";
        $sql.= "expiry = '".$expiry."', value = '".$value."' ";
        $sql.= "where skey = '".$skey."' ";
        return mysql_query($sql, self::$CONN);
    }

    public static function sessDestroy($skey)
    {
        $sql = "delete from ".self::TABLE." ";
        $sql.= "where skey = '".$skey."' ";
        return mysql_query($sql, self::$CONN);
    }

    public static function sessGc()
    {
        $sql = "delete from ".self::TABLE." ";
        $sql.= "where expiry < ".time()." ";
        $row = mysql_query($sql, self::$CONN);
        return mysql_affected_rows(self::$CONN);
    }
}