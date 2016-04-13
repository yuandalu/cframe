<?php

namespace App\Models\Svc;

use Elephant\Container\Container;
use App\Lib\DBCache;
use Elephant\Db\SQLExecutor;

class LoaderSvc
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
        self::setMasterRedisConf($master_redis);
        self::setSlaveRedisConf($slave_redis);
    }

    public static function setExecutorConf()
    {
        self::$_db = array(
            'host' => $_SERVER['ENV_DB_HOST'],
            'port' => $_SERVER['ENV_DB_PORT'],
            'user' => $_SERVER['ENV_DB_USER'],
            'pass' => $_SERVER['ENV_DB_PASS'],
            'name' => $_SERVER['ENV_DB_NAME'],
        );
    }

    public static function setSlaveExecutorConf()
    {
        self::$_db_r = array(
            'host' => $_SERVER['ENV_DB_HOST_R'],
            'port' => $_SERVER['ENV_DB_PORT_R'],
            'user' => $_SERVER['ENV_DB_USER_R'],
            'pass' => $_SERVER['ENV_DB_PASS_R'],
            'name' => $_SERVER['ENV_DB_NAME_R'],
        );
    }

    public static function setFinanceExecutorConf()
    {
        self::$_db_f = array(
            'host' => $_SERVER['ENV_FINANCE_DB_HOST'],
            'port' => $_SERVER['ENV_FINANCE_DB_PORT'],
            'user' => $_SERVER['ENV_FINANCE_DB_USER'],
            'pass' => $_SERVER['ENV_FINANCE_DB_PASS'],
            'name' => $_SERVER['ENV_FINANCE_DB_NAME'],
        );
    }

    public static function setVlogExecutorConf()
    {
        self::$_db_vlog = array(
            'host' => $_SERVER['ENV_VLOG_DB_HOST'],
            'port' => $_SERVER['ENV_VLOG_DB_PORT'],
            'user' => $_SERVER['ENV_VLOG_DB_USER'],
            'pass' => $_SERVER['ENV_VLOG_DB_PASS'],
            'name' => $_SERVER['ENV_VLOG_DB_NAME'],
        );
    }

    public static function loadExecutor()
    {
        $obj = Container::find('SQLExecutor');
        if (is_object($obj)) {
            return $obj;
        }

        if (is_null(self::$_db)) {
            return null;
        }

        $obj = new SQLExecutor(self::$_db);
        $obj->regLogObj(LogSvc::getSqlLog());
        Container::register('SQLExecutor', $obj);
        return $obj;
    }
    public static function loadSlaveExecutor()
    {
        $obj = Container::find('SQLExecutorSlave');
        if (is_object($obj)) {
            return $obj;
        }

        if (is_null(self::$_db_r)) {
            return null;
        }

        $obj = new SQLExecutor(self::$_db_r);
        $obj->regLogObj(LogSvc::getSqlLog());
        Container::register('SQLExecutorSlave', $obj);
        return $obj;
    }
    public static function loadFinanceExecutor()
    {
        $obj = Container::find('SQLExecutorFinance');
        if (is_object($obj)) {
            return $obj;
        }

        if (is_null(self::$_db_f)) {
            return null;
        }

        $obj = new SQLExecutor(self::$_db_f);
        $obj->regLogObj(LogSvc::getFinanceSqlLog());
        Container::register('SQLExecutorFinance', $obj);
        return $obj;
    }

    public static function loadVlogExecutor()
    {
        $obj = Container::find('SQLExecutorVlog');
        if (is_object($obj)) {
            return $obj;
        }

        if (is_null(self::$_db_vlog)) {
            return null;
        }

        $obj = new SQLExecutor(self::$_db_vlog);
        $obj->regLogObj(LogSvc::getFinanceSqlLog());
        Container::register('SQLExecutorVlog', $obj);
        return $obj;
    }

    public static function loadIdGenter()
    {
        $obj = Container::find('IDGenter');
        if (is_object($obj)) {
            return $obj;
        }

        $obj = new IDGenter(self::loadExecutor());
        Container::register('IDGenter', $obj);
        return $obj;
    }

    public static function loadFinanceIdGenter()
    {
        $obj = Container::find('FinanceIDGenter');
        if (is_object($obj)) {
            return $obj;
        }

        $obj = new IDGenter(self::loadFinanceExecutor());
        Container::register('FinanceIDGenter', $obj);
        return $obj;
    }

    public static function setCacheConf()
    {
        $conf = explode(',',$_SERVER['ENV_MEMCACHED_SERVERS']);

        $servers = array();
        foreach($conf as $server) {
            list($host, $port) = explode(':',$server);
            $servers[] = array('host'=>$host, 'port'=>$port);
        }
        self::$_cache = $servers;
    }

    public static function setMasterRedisConf($conf)
    {
        if (!is_array($conf)) {
            return;
        }
        self::$_master_redis = $conf;
    }

    public static function setSlaveRedisConf($conf)
    {
        if (!is_array($conf)) {
            return;
        }
        self::$_slave_redis = $conf;
    }


    public static function loadCache()
    {
        $obj = Container::find('MemCacheDriver');
        if (is_object($obj)) {
            return $obj;
        }

        if (!is_array(self::$_cache)) {
            return null;
        }

        $obj = new MemCacheDriver(self::$_cache);
        Container::register('MemCacheDriver', $obj);
        return $obj;
    }

    public static function loadTT()
    {
        $obj = Container::find('TTDriver');

        if (is_object($obj)) {
            return $obj;
        }
        list($host, $port) = explode(':',$_SERVER['ENV_TT_SERVERS']);
        $obj = new TTDriver();
        $obj->connect($host,$port);
        Container::register('TTDriver', $obj);
        return $obj;
    }

    public static function loadMasterRedis()
    {
        $obj = Container::find('MasterRedis');
        if (is_object($obj)) {
            return $obj;
        }
        list($host, $port) = explode(':',$_SERVER['ENV_REDIS_SERVER']);
        $obj = new MasterRedis($host, $port);
        Container::register('MasterRedis', $obj);
        return $obj;
    }

    public static function loadSlaveRedis()
    {
        $obj = Container::find('SlaveRedis');
        if (is_object($obj)) {
            return $obj;
        }
        list($host, $port) = explode(':',$_SERVER['ENV_REDIS_SERVER_R']);
        $obj = new SlaveRedis($host, $port);
        Container::register('SlaveRedis', $obj);
        return $obj;
    }

    public static function loadHttpsqs()
    {
        $obj = Container::find('HttpsqsDriver');
        if (is_object($obj)) {
            return $obj;
        }
        list($host, $port) = explode(':',$_SERVER['ENV_HTTPSQS_SERVER']);
        $obj = new HttpsqsDriver($host, $port);
        Container::register('HttpsqsDriver', $obj);
        return $obj;
    }

    public static function loadDao($entity)
    {
        $cls = $entity.'Dao';
        $dao = Container::find($cls);
        if (is_object($dao)) {
            return $dao;
        }

        $dao = new $cls();
        Container::register($cls, $dao);
        return $dao;
    }

    public static function regSess($name)
    {
        $obj = new MysqlSessDriver();
        $svc = new SessionSvc($name, $obj);
        Container::register('SessSvc', $svc);
    }

    public static function loadSess()
    {
        return Container::find('SessSvc');
    }

    public static function loadDBCache()
    {
        $obj = new DBCache();
        Container::register('DBCache', $obj);
        return Container::find('DBCache');
    }

}