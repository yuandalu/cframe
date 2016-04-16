<?php

namespace App\Models\Svc;

use App\Support\Loader;
use App\Models\Entity\AdmMenu;

class AdmMenuSvc
{
    const OBJ = 'AdmMenu';
    #warning 书写格式没有规范
    public static function add($param)
    {
        $obj = AdmMenu::createByBiz($param);
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

    public static function getMenus($param ,$super)
    {
        return self::getDao()->getMenus($param,$super);
    }
    public static function getByUid($uid = '0')
    {
        return self::getDao()->getByUid($uid);
    }

    public static function getByName($name)
    {
        return self::getDao()->where('name', $name)->find();
    }

    private static function getDao()
    {
        return Loader::loadDao(self::OBJ);
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
            $userinfo =  UserSdk::getInfoByUsername($request['username']);
            $query_uid = $userinfo['uid'];
            $request_param[] = 'username=' . urlencode($request['username']);
            $sql_condition[] = 'uid=?'  ;
            $sql_param[]     = $query_uid;
        }


        if ($request['name']) {
            $request_param[] = 'name=' . $request['name'];
            $sql_condition[] = 'name =?';
            $sql_param[]     = $request['name'];
        }
        if ($request['url']) {
            $request_param[] = 'url=' . $request['url'];
            $sql_condition[] = 'url =?';
            $sql_param[]     = $request['url'];
        }
        if ($request['sort']) {
            $request_param[] = 'sort=' . $request['sort'];
            $sql_condition[] = 'sort =?';
            $sql_param[]     = $request['sort'];
        }
        if ($request['groupid']) {
            $request_param[] = 'groupid=' . $request['groupid'];
            $sql_condition[] = 'groupid =?';
            $sql_param[]     = $request['groupid'];
        }
        if ($request['aid']) {
            $request_param[] = 'aid=' . $request['aid'];
            $sql_condition[] = 'aid =?';
            $sql_param[]     = $request['aid'];
        }
        if ($request['oneclass']) {
            $request_param[] = 'oneclass=' . $request['oneclass'];
            $sql_condition[] = 'oneclass =?';
            $sql_param[]     = $request['oneclass'];
        }
        if ($request['curr_menu']) {
            $request_param[] = 'curr_menu=' . $request['curr_menu'];
            $sql_condition[] = 'curr_menu =?';
            $sql_param[]     = $request['curr_menu'];
        }
        if ($request['curr_submenu']) {
            $request_param[] = 'curr_submenu=' . $request['curr_submenu'];
            $sql_condition[] = 'curr_submenu =?';
            $sql_param[]     = $request['curr_submenu'];
        }
        //print_r($sql_condition);print_r($sql_param);exit;
        return self::getDao()->getPager($request_param, $sql_condition,$sql_param , $options, $export);
    }
    
    public static function getAll()
    {
        $data = self::getDao()->getAll($tag);
        return $data;
    }

    public static function getOneclass()
    {
        return self::getDao()->getOneclass();
    }

    public static function getByonclass($oneclass)
    {
        return self::getDao()->getByonclass($oneclass);
    }

}