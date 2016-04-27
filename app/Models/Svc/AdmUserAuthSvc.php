<?php

namespace App\Models\Svc;

use App\Support\Loader;
use App\Models\Entity\AdmUserAuth;

class AdmUserAuthSvc
{
    const OBJ = 'AdmUserAuth';

    public static function add($param)
    {
        $obj = AdmUserAuth::createByBiz($param);
        return self::getDao()->add($obj);
    }
    public static function getById($id = '0')
    {
        if (empty($id))
        {
            return null;
        }
        return self::getDao()->getById($id);
    }

    public static function updateById($id, $param)
    {
        return self::getDao()->updateById($id, $param);
    }

    public static function getByUid($uid = '0')
    {
        return self::getDao()->getByUid($uid);
    }
    public static function getAid()
    {
        return self::getDao()->getAid();
    }
    public static function verifyUidAid($uid, $aid)
    {
        return self::getDao()->where('uid', $uid)->where('aid', $aid)->find();
    }
    private static function getDao()
    {
        return Loader::loadDao(self::OBJ);
    }

    public static function lists($request=array(), $options=array(), $export = false)
    {
        $request_param = array();
        $sql_condition = array();
        $sql_param     = array();

        if (isset($request['id']) && strlen($request['id'])>1) {
            $request_param[] = 'id=' . $request['id'];
            $sql_condition[] = 'id = ? '  ;
            $sql_param[]     = $request['id'];
        }

        if ($request['startdate'] != '') {
            $request_param[] = 'startdate=' . $request['startdate'];
            $sql_condition[] = 'ctime>=?';
            $sql_param[]     = $request['startdate'];
        }
        if ($request['enddate'] != '') {
            $request_param[] = 'enddate=' . $request['enddate'];
            $sql_condition[] = 'ctime<=?';
            if ('10' >= strlen($request['enddate'])) {
                $sql_param[] = $request['enddate'].' 23:59:59';
            } else {
                $sql_param[] = $request['enddate'];
            }
        }


        if ($request['username'] != '') {
            $userinfo =  UserSdk::getInfoByUsername($request['username']);
            $query_uid = $userinfo['uid'];
            $request_param[] = 'username=' . urlencode($request['username']);
            $sql_condition[] = 'uid=?'  ;
            $sql_param[]     = $query_uid;
        }


        if ($request['user']) {
            $request_param[] = 'user=' . $request['user'];
            $sql_condition[] = 'user =?';
            $sql_param[]     = $request['user'];
        }
        if ($request['uid']) {
            $request_param[] = 'uid=' . $request['uid'];
            $sql_condition[] = 'uid =?';
            $sql_param[]     = $request['uid'];
        }
        if ($request['aid']) {
            $request_param[] = 'aid=' . $request['aid'];
            $sql_condition[] = 'aid =?';
            $sql_param[]     = $request['aid'];
        }
        return self::getDao()->getPager($request_param, $sql_condition, $sql_param, $options, $export);
    }

}