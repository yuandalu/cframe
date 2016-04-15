<?php

namespace App\Models\Svc;

use App\Support\Loader;
use App\Models\Entity\AdmUser;

class AdmUserSvc
{
    const OBJ = 'AdmUser';

    public static function add($param)
    {
        $obj = AdmUser::createByBiz($param);
        $addResult = self::getDao()->add($obj);
        return $addResult;
    }

    public static function getByEname($ename)
    {
        return self::getDao()->getByEname($ename);
    }
    public static function updateById($id, $param)
    {
        $result = self::getDao()->updateById($id, $param);
        return $result;
    }
    public static function lists($request=array(), $options=array())
    {
        $request_param = array();
        $sql_condition = array();
        
        if ($request['name']) {
            $request_param[] = 'name=' . $request['name'];
            $sql_condition[] = 'name =? or ename=?';
            $sql_param[]     = $request['name'];
            $sql_param[]     = $request['name'];
        }
        return self::getDao()->getPager($request_param, $sql_condition, $sql_param, $options );
    }

    public static function forbiddenAccount($id)
    {
        return self::getDao()->forbiddenAccount($id);
    }

    public static function delauth($param)
    {
        return self::getDao()->delauth($param);
    }
    public static function getById($id)
    {

        return self::getDao()->getById($id);
    }

    public static function getUidByEname($name)
    {
        return self::getDao()->getUidByEname($name);
    }

    public static function getAuthByUid($uid)
    {
        return self::getDao()->getAuthByUid($uid);
    }
    public static function getUidByAuth($aid)
    {
        return self::getDao()->getUidByAuth($aid);
    }

    public static function getAdminByuid($uid)
    {
        return self::getDao()->getAdminByuid($uid);
    }

    public static function checkName($name)
    {
        return self::getDao()->checkName($name);
    }
    public static function addAuth($param)
    {
        return self::getDao()->addAuth($param);
    }
    private static function getDao()
    {
        return Loader::loadDao(self::OBJ);
    }
}