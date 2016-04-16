<?php

namespace App\Models\Svc;

use App\Support\Loader;
use App\Models\Entity\AdmAuth;

class AdmAuthSvc
{
    const OBJ = 'AdmAuth';
    public static function add($param)
    {
        $obj = AdmAuth::createByBiz($param);
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
    #warning 书写格式没有规范
    public static function verify($c,$a)
    {
        $admin = AdminSvc::getLoginUser();
        $auths = self::getDao()->getAidByUser($admin);
        $data  = AdmAuthNodeSvc::verify($c, $a);
        /*echo "<pre>";
        print_r($data);
        echo "所有权限";
        echo "<hr>";
        print_r($auths);
        echo "我的权限";
        exit;*/
        foreach ($data as $value) {
            if (in_array($value,$auths)) {
                return "succ";
            }
        }
        return "fail";

    }
    public static function getConf()
    {
        $all = self::getDao()->getAll();
        $conf = array();
        foreach($all as $v)
        {
            $conf[$v['id']] = $v['name'];
        }
        return $conf;
    }
    public static function getAll()
    {
        return self::getDao()->getAll();
    }
    public static function getAid()
    {
        return self::getDao()->getAid();
    }
    public static function updateById($id, $param)
    {
        return self::getDao()->updateById($id, $param);
    }

    public static function getByUid($uid = '0')
    {
        return self::getDao()->getByUid($uid);
    }

    public static function getByName($name)
    {
        return self::getDao()->where('name', $name)->find();
    }
    public static function getAuthByUid($uid)
    {
        return self::getDao()->getAuthByUid($uid);
    }
    private static function getDao()
    {
        return Loader::loadDao(self::OBJ);
    }

    public static function lists($request=array(), $options=array(), $export = false)
    {
        $request_param = array();
        $sql_condition = array();

        if(isset($request['id']) && strlen($request['id'])>1)
        {
            $request_param[] = 'id=' . $request['id'];
            $sql_condition[] = 'id = ? '  ;
            $sql_param[]     = $request['id'];
        }

        if($request['startdate'] != '')
        {
            $request_param[] = 'startdate=' . $request['startdate'];
            $sql_condition[] = 'ctime>=?';
            $sql_param[]     = $request['startdate'];
        }
        if($request['enddate'] != '')
        {
            $request_param[] = 'enddate=' . $request['enddate'];
            $sql_condition[] = 'ctime<=?';
            if('10' >= strlen($request['enddate']) )
            {
                $sql_param[] = $request['enddate'].' 23:59:59';
            }
            else
            {
                $sql_param[] = $request['enddate'];
            }
        }


        if($request['username'] != '')
        {
            $userinfo =  UserSdk::getInfoByUsername($request['username']);
            $query_uid = $userinfo['uid'];
            $request_param[] = 'username=' . urlencode($request['username']);
            $sql_condition[] = 'uid=?'  ;
            $sql_param[]     = $query_uid;
        }


        if($request['name'])
        {
            $request_param[] = 'name=' . $request['name'];
            $sql_condition[] = 'name =?';
            $sql_param[]     = $request['name'];
        }
        //print_r($sql_condition);print_r($sql_param);exit;
        return self::getDao()->getPager($request_param, $sql_condition,$sql_param , $options, $export);
    }

    public static function getauth()
    {
        return self::getDao()->getauth();
    }

    public static function getAllauth()
    {
        return self::getDao()->getAllauth();
    }

}