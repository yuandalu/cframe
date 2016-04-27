<?php

namespace App\Models\Svc;

use App\Support\Loader;

class LogSvc
{
    const OBJ = 'Log';

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
        $request_param = array();
        $sql_condition = array();
        $sql_param     = array();

        if (isset($request['username'])) {
            $request_param[] = 'username='.$request['username'];
            $sql_condition[] = 'username=?';
            $sql_param[] = $request['username'];
        }
        if (isset($request['kw'])) {
            $request_param[] = 'kw='.$request['kw'];
            $sql_condition[] = '`content` like ?';
            $sql_param[] = '%'.$request['kw'].'%';
        }
        if (isset($request['start'])) {
            $request_param[] = 'start='.$request['start'];
            $sql_condition[] = 'action_time >= ?';
            $sql_param[] = $request['start'];
        }
        if (isset($request['end'])) {
            $request_param[] = 'end='.$request['end'];
            $sql_condition[] = 'action_time<=?';
            if ('10' >= strlen($request['end'])) {
                $sql_param[] = $request['end'].' 23:59:59';
            } else {
                $sql_param[] = $request['end'];
            }
        }

        return self::getDao()->getLogs($table, $options, $request, $request_param, $sql_condition, $sql_param);
    }

    public static function getDetailByid($id)
    {
        return self::getDao()->getDetailByid($id);
    }

    private static function getDao()
    {
        return Loader::loadDao(self::OBJ);
    }
}