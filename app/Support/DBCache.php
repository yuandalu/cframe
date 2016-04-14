<?php

namespace App\Support;

class DBCache
{
    const TABLE_NAME = 'sys_dbcache';
    const REMOTE_KEY = "o6fk&(#@fdbsd";
    static $executor = null;

    public function getExecutor()
    {
        return Loader::loadExecutor();
    }

    public function get($skey)
    {
        $sql    = "select value,expiry from ".self::TABLE_NAME." where skey=? and expiry>?";
        $args   = array(md5($skey), time());
        $result = self::getExecutor()->query($sql,$args);
        if (!empty($result)) {
            return unserialize($result['value']);
        } else {
            return false;
        }
    }

    public function set($skey, $value, $expiry = 86400)
    {
        $sql  = "replace into ".self::TABLE_NAME." (skey,expiry,value)values(?,?,?)";
        $args = array(md5($skey), $expiry+time(),serialize($value));
        return self::getExecutor()->exeNoQuery($sql,$args);
    }

    public function destroy($skey)
    {
        $sql  = "delete from ".self::TABLE_NAME." where skey=?";
        $args = array(md5($skey));
        return self::getExecutor()->exeNoQuery($sql,$args);
    }

    public function flush()
    {
        $sql = "delete from ".self::TABLE_NAME. " where expiry<?";
        return self::getExecutor()->exeNoQuery($sql,array(time()));
    }

    public function flush_all()
    {
        //为了安全 不执行任何操作
        //$sql = "truncate ".self::TABLE_NAME;
        //return $this->getExecutor()->exeNoQuery($sql);
    }
}