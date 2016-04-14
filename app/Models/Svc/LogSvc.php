<?php

namespace App\Models\Svc;

use App\Support\Loader;

class LogSvc
{
    const OBJ = 'Log';
    #warning 查一下如果有相关的都替换为log()
    // public static function getBizErrLog()
    // {
    //     return self::getLogTpl('biz_err', true);
    // }

    // public static function getSysErrLog()
    // {
    //     return self::getLogTpl('sys_err', true);
    // }

    // public static function getQuerySecurityLog()
    // {
    //     return self::getLogTpl('admin_query_security', true);
    // }

    // public static function getPayNotifyLog()
    // {
    //     return self::getLogTpl('pay_notify', true);
    // }

    public static function loginLog($param)
    {
        return self::getDao()->loginLog($param);
    }

    public static function writeLog($action, $content)
    {
        return self::getDao()->writeLog($action, $content);
    }

    public static function getLogs($table, $options=array(), $request = array())
    {

        if($request['username'])
        {
            $request_param[] = 'username='.$request['username'];
            $sql_condition[] = 'username=?';
            $sql_param[] = $request['username'];
        }
        if($request['kw'])
        {
            $request_param[] = 'kw='.$request['kw'];
            $sql_condition[] = '`content` like ?';
            $sql_param[] = '%'.$request['kw'].'%';
        }
        if($request['start'])
        {
            $request_param[] = 'start='.$request['start'];
            $sql_condition[] = 'action_time >= ?';
            $sql_param[] = $request['start'];
        }
        if($request['end'])
        {
            $request_param[] = 'end='.$request['end'];
            $sql_condition[] = 'action_time<=?';
            $sql_param[] = $request['end'];
        
        }

        return self::getDao()->getLogs($table,$options, $request, $request_param, $sql_condition,$sql_param);
    }

    public static function getDetailByid($id)
    {
        return self::getDao()->getDetailByid($id);
    }

    public static function delLog($id)
    {
        return self::getDao()->delLog($id);
    }

    public static function getRunningLog($id)
    {
        return self::getDao()->getRunningLog($id);
    }

    private static function getDao()
    {
        return Loader::loadDao(self::OBJ);
    }
}