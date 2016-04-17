<?php

namespace App\Models\Svc;

use App\Support\Loader;
use App\Models\Entity\User;


class UserSvc
{
    const OBJ = 'User';

    public static function add($param)
    {
        $obj = User::createByBiz($param);
        return self::getDao()->add($obj);
    }

    public static function updateById($id, $param)
    {
        return self::getDao()->updateById($id, $param, self::OBJ);
    }

    public static function getById($id = '0')
    {
        return self::getDao()->getById($id , self::OBJ);
    }

    public static function deleteById($id)
    {
        return self::getDao()->deleteById($id, self::OBJ);
    }

    public static function getAll()
    {
        return self::getDao()->gets();
    }

    private static function getDao()
    {
        return Loader::loadDao(self::OBJ);
    }

    public static function lists($request=array(), $options=array(), $export = false)
    {
        $request_param = array();
        $sql_condition = array();

        if (isset($request['id']) && $request['id'] > 0) {
            $request_param[] = 'id='.$request['id'];
            $sql_condition[] = 'id = ?';
            $sql_param[]     = $request['id'];
        }

        if ($request['startdate'] != '') {
            $request_param[] = 'startdate='.$request['startdate'];
            $sql_condition[] = 'ctime >= ?';
            if ('10' >= strlen($request['startdate'])) {
                $sql_param[] = $request['startdate'].' 00:00:00';
            } else {
                $sql_param[] = $request['startdate'];
            }
        }
        if ($request['enddate'] != '') {
            $request_param[] = 'enddate='.$request['enddate'];
            $sql_condition[] = 'ctime <= ?';
            if ('10' >= strlen($request['enddate'])) {
                $sql_param[] = $request['enddate'].' 23:59:59';
            } else {
                $sql_param[] = $request['enddate'];
            }
        }

        if ($options['orderby']) {
            $request_param[] = 'orderby='.urlencode($options['orderby']);
        }

        if ($request['mobile']) {
            $request_param[] = 'mobile='.$request['mobile'];
            $sql_condition[] = 'mobile = ?';
            $sql_param[]    = $request['mobile'];
        }
        if ($request['nickname']) {
            $request_param[] = 'nickname='.$request['nickname'];
            $sql_condition[] = 'nickname = ?';
            $sql_param[]    = $request['nickname'];
        }
        if ($request['password']) {
            $request_param[] = 'password='.$request['password'];
            $sql_condition[] = 'password = ?';
            $sql_param[]    = $request['password'];
        }
        if ($request['salt']) {
            $request_param[] = 'salt='.$request['salt'];
            $sql_condition[] = 'salt = ?';
            $sql_param[]    = $request['salt'];
        }
        if ($request['status']) {
            $request_param[] = 'status='.$request['status'];
            $sql_condition[] = 'status = ?';
            $sql_param[]    = $request['status'];
        }
        return self::getDao()->getPager($request_param, $sql_condition,$sql_param , $options, $export);
    }

}
