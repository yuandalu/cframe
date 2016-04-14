<?php

namespace App\Models\Svc;

use App\Support\Loader;

class AdmAuthNodeSvc
{
    const OBJ = 'AdmAuthNode';
    public static function add($param)
    {
        $obj = AdmAuthNode::createByBiz($param);
        return self::getDao()->add($obj);
    }
    public static function getById($id = '0')
    {
        if (empty($id)) {
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
    
    private static function getDao()
    {
        return Loader::loadDao(self::OBJ);
    }
    public static function getAll()
    {
        return self::getDao()->getAll();
    }
    public static function lists($request=array(), $options=array(), $export = false)
    {
        $request_param = array();
        $sql_condition = array();



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
            if ('10' >= strlen($request['enddate']) ) {
                $sql_param[] = $request['enddate'].' 23:59:59';
            } else {
                $sql_param[] = $request['enddate'];
            }
        }

        if ($request['username'] != '') {
            $userinfo  =  $request['username'];
            $query_uid = $userinfo['uid'];
            $request_param[] = 'username=' . urlencode($request['username']);
            $sql_condition[] = 'uid=?'  ;
            $sql_param[]     = $query_uid;
        }

        if ($request['aid']) {
            $request_param[] = 'aid=' . $request['aid'];
            $sql_condition[] = 'aid =?';
            $sql_param[]     = $request['aid'];
        }
        if ($request['contr']) {
            $request_param[] = 'contr=' . $request['contr'];
            $sql_condition[] = 'contr =?';
            $sql_param[]     = $request['contr'];
        }
        if ($request['action']) {
            $request_param[] = 'action=' . $request['action'];
            $sql_condition[] = 'action =?';
            $sql_param[]     = $request['action'];
        }
        //print_r($sql_condition);print_r($sql_param);exit;
        return self::getDao()->getPager($request_param, $sql_condition,$sql_param , $options, $export);
    }

    public static function getControauth()
    {
        return self::getDao()->getControauth();
    }

    public static function getActionauth()
    {
        return self::getDao()->getActionauth();
    }
    public static function verify($c,$a)
    {
        return self::getDao()->verify($c,$a);
    }
    public static function updateByCA($param)
    {
        return self::getDao()->updateByCA($param);
    }

}