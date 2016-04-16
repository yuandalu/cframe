<?php

namespace App\Support;

class Loader
{
    private static $_db     = null;
    private static $_db_r   = null;
    private static $_db_f   = null;
    private static $_db_vlog= null;
    private static $_cache  = null;

    public static function init()
    {
        self::setExecutorConf();
        self::setSlaveExecutorConf();
        self::setFinanceExecutorConf();
        self::setVlogExecutorConf();
        self::setCacheConf();
    }

    public static function setExecutorConf()
    {
        self::$_db = array(
            'host' => env('ENV_DB_HOST'),
            'port' => env('ENV_DB_PORT'),
            'user' => env('ENV_DB_USER'),
            'pass' => env('ENV_DB_PASS'),
            'name' => env('ENV_DB_NAME'),
        );
    }

    public static function setSlaveExecutorConf()
    {
        self::$_db_r = array(
            'host' => env('ENV_DB_HOST_R'),
            'port' => env('ENV_DB_PORT_R'),
            'user' => env('ENV_DB_USER_R'),
            'pass' => env('ENV_DB_PASS_R'),
            'name' => env('ENV_DB_NAME_R'),
        );
    }

    public static function setFinanceExecutorConf()
    {
        self::$_db_f = array(
            'host' => env('ENV_FINANCE_DB_HOST'),
            'port' => env('ENV_FINANCE_DB_PORT'),
            'user' => env('ENV_FINANCE_DB_USER'),
            'pass' => env('ENV_FINANCE_DB_PASS'),
            'name' => env('ENV_FINANCE_DB_NAME'),
        );
    }

    public static function setVlogExecutorConf()
    {
        self::$_db_vlog = array(
            'host' => env('ENV_VLOG_DB_HOST'),
            'port' => env('ENV_VLOG_DB_PORT'),
            'user' => env('ENV_VLOG_DB_USER'),
            'pass' => env('ENV_VLOG_DB_PASS'),
            'name' => env('ENV_VLOG_DB_NAME'),
        );
    }

    public static function loadExecutor()
    {
        $obj = ObjectFinder::find('SQLExecutor');
        if (is_object($obj)) {
            return $obj;
        }

        if (is_null(self::$_db)) {
            return null;
        }

        $obj = new SQLExecutor(self::$_db);
        $obj->regLogObj(logs('sql'));
        ObjectFinder::register('SQLExecutor', $obj);
        return $obj;
    }
    public static function loadSlaveExecutor()
    {
        $obj = ObjectFinder::find('SQLExecutorSlave');
        if (is_object($obj)) {
            return $obj;
        }

        if (is_null(self::$_db_r)) {
            return null;
        }

        $obj = new SQLExecutor(self::$_db_r);
        $obj->regLogObj(logs('sql'));
        ObjectFinder::register('SQLExecutorSlave', $obj);
        return $obj;
    }
    public static function loadFinanceExecutor()
    {
        $obj = ObjectFinder::find('SQLExecutorFinance');
        if (is_object($obj)) {
            return $obj;
        }

        if (is_null(self::$_db_f)) {
            return null;
        }

        $obj = new SQLExecutor(self::$_db_f);
        $obj->regLogObj(logs('finance_sql'));
        ObjectFinder::register('SQLExecutorFinance', $obj);
        return $obj;
    }

    public static function loadLogExecutor()
    {
        $obj = ObjectFinder::find('SQLExecutorLog');
        if (is_object($obj)) {
            return $obj;
        }

        if (is_null(self::$_db_vlog)) {
            return null;
        }

        $obj = new SQLExecutor(self::$_db_vlog);
        $obj->regLogObj(logs('log_sql'));
        ObjectFinder::register('SQLExecutorLog', $obj);
        return $obj;
    }

    public static function loadIdGenter()
    {
        $obj = ObjectFinder::find('IDGenter');
        if (is_object($obj)) {
            return $obj;
        }

        $obj = new IDGenter(self::loadExecutor());
        ObjectFinder::register('IDGenter', $obj);
        return $obj;
    }

    public static function loadFinanceIdGenter()
    {
        $obj = ObjectFinder::find('FinanceIDGenter');
        if (is_object($obj)) {
            return $obj;
        }

        $obj = new IDGenter(self::loadFinanceExecutor());
        ObjectFinder::register('FinanceIDGenter', $obj);
        return $obj;
    }

    public static function setCacheConf()
    {
        $conf = explode(',',env('ENV_MEMCACHED_SERVERS'));

        $servers = array();
        foreach($conf as $server) {
            list($host, $port, $user, $password) = explode(':',$server);
            $servers[] = array('host'=>$host, 'port'=>$port, 'user'=>$user, 'password'=>$password);
        }
        self::$_cache = $servers;
    }


    public static function loadCache()
    {
        $obj = ObjectFinder::find('MemCacheDriver');
        if (is_object($obj)) {
            return $obj;
        }

        if (!is_array(self::$_cache)) {
            return null;
        }

        $obj = new MemCacheDriver(self::$_cache);
        ObjectFinder::register('MemCacheDriver', $obj);
        return $obj;
    }

    public static function loadMasterRedis()
    {
        $obj = ObjectFinder::find('MasterRedis');
        if (is_object($obj)) {
            return $obj;
        }
        list($host, $port) = explode(':',env('ENV_REDIS_SERVER'));
        $obj = new MasterRedis($host, $port);
        $auth = env('ENV_REDIS_SERVER_AUTH');
        if (!empty($auth)) {
            $obj->auth($auth);
        }
        ObjectFinder::register('MasterRedis', $obj);
        return $obj;
    }

    public static function loadSlaveRedis()
    {
        $obj = ObjectFinder::find('SlaveRedis');
        if (is_object($obj)) {
            return $obj;
        }
        list($host, $port) = explode(':',env('ENV_REDIS_SERVER_R'));
        $obj  = new SlaveRedis($host, $port);
        $auth = env('ENV_REDIS_SERVER_AUTH_R');
        if (!empty($auth)) {
            $obj->auth($auth);
        }
        ObjectFinder::register('SlaveRedis', $obj);
        return $obj;
    }

    public static function loadHttpsqs()
    {
        $obj = ObjectFinder::find('HttpsqsDriver');
        if (is_object($obj)) {
            return $obj;
        }
        list($host, $port) = explode(':',env('ENV_HTTPSQS_SERVER'));
        $obj = new HttpsqsDriver($host, $port);
        ObjectFinder::register('HttpsqsDriver', $obj);
        return $obj;
    }

    public static function loadDao($entity)
    {
        $cls = '\App\Models\Dao\\'.$entity.'Dao';
        $dao = ObjectFinder::find($cls);
        if (is_object($dao)) {
            return $dao;
        }

        $dao = new $cls();
        ObjectFinder::register($cls, $dao);
        return $dao;
    }

    public static function regSess($name)
    {
        $obj  = new SessMysqlDriver();
        $sess = new Sess($name, $obj);
        ObjectFinder::register('Sess', $sess);
    }

    public static function loadSess()
    {
        return ObjectFinder::find('Sess');
    }

    public static function loadDBCache()
    {
        $obj = ObjectFinder::find('DBCache');
        if (is_object($obj)) {
            return $obj;
        }
        $obj = new DBCache();
        ObjectFinder::register('DBCache', $obj);
        return ObjectFinder::find('DBCache');
    }

}