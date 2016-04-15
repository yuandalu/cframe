<?php

namespace App\Models\Svc;

use App\Support\Loader;
use App\Models\Entity\AdmUserAuth;

class AdmUserAuthSvc
{
    const OBJ = 'AdmUserAuth';
    #warning 书写格式没有规范
    static public function add($param)
    {
        $obj = AdmUserAuth::createByBiz($param);
        return self::getDao()->add($obj);
    }
    static public function getById($id = '0')
    {
        if (empty($id))
        {
            return null;
        }
        return self::getDao()->getById($id);
    }

    static public function updateById($id, $param)
    {
        return self::getDao()->updateById($id, $param);
    }

    static public function getByUid($uid = '0')
    {
        return self::getDao()->getByUid($uid);
    }
    static public function getAid()
    {
        return self::getDao()->getAid();
    }
    private static function getDao()
    {
        return Loader::loadDao(self::OBJ);
    }

    static public function lists($request=array(), $options=array(), $export = false)
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


        if($request['user'])
        {
            $request_param[] = 'user=' . $request['user'];
            $sql_condition[] = 'user =?';
            $sql_param[]     = $request['user'];
        }
        if($request['uid'])
        {
            $request_param[] = 'uid=' . $request['uid'];
            $sql_condition[] = 'uid =?';
            $sql_param[]     = $request['uid'];
        }
        if($request['aid'])
        {
            $request_param[] = 'aid=' . $request['aid'];
            $sql_condition[] = 'aid =?';
            $sql_param[]     = $request['aid'];
        }
        //print_r($sql_condition);print_r($sql_param);exit;
        return self::getDao()->getPager($request_param, $sql_condition,$sql_param , $options, $export);
    }

}