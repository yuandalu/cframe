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

    const TABLE = 'sys_sessions';

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
        if (!env('READONLY_MODE', 'local')) {
            $handler = new SessHandler();
            session_set_save_handler($handler, true);
        }
    }

    public static function sessOpen()
    {
        self::$CONN = mysqli_connect(self::$HOST.':'.self::$PORT, self::$USER, self::$PASS, self::$NAME);

        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }

        return true;
    }

    public static function sessClose()
    {
        if (is_resource(self::$CONN)) {
            mysqli_close(self::$CONN);
        }
        return true;
    }

    public static function sessRead($skey)
    {
        $sql = "select value ";
        $sql.= "from ".self::TABLE." ";
        $sql.= "where skey = '".$skey."' ";
        $sql.= "and expiry > '".time()."' ";
        $row = mysqli_query(self::$CONN, $sql);

        if (list($result) = mysqli_fetch_row($row)) {
            return $result;
        }
        return false;
    }

    public static function sessWrite($skey, $value)
    {
        $skey   = mysqli_real_escape_string(self::$CONN, $skey);
        $value  = mysqli_real_escape_string(self::$CONN, $value);
        $expiry = time() + self::$LIFE;

        $sql = "insert into ".self::TABLE." ";
        $sql.= "values ('".$skey."', '".$expiry."', '".$value."') ";
        $row = mysqli_query(self::$CONN, $sql);
        if ($row) {
            return $row;
        }

        $sql = "update ".self::TABLE." set ";
        $sql.= "expiry = '".$expiry."', value = '".$value."' ";
        $sql.= "where skey = '".$skey."' ";
        return mysqli_query(self::$CONN, $sql);
    }

    public static function sessDestroy($skey)
    {
        $sql = "delete from ".self::TABLE." ";
        $sql.= "where skey = '".$skey."' ";
        return mysqli_query(self::$CONN, $sql);
    }

    public static function sessGc()
    {
        $sql = "delete from ".self::TABLE." ";
        $sql.= "where expiry < ".time()." ";
        $row = mysqli_query(self::$CONN, $sql);
        return mysqli_affected_rows(self::$CONN);
    }
}