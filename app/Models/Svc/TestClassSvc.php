<?php

namespace App\Models\Svc;

use App\Support\Loader;
use App\Models\Entity\TestClass;


class TestClassSvc
{
    const OBJ = 'TestClass';

    public static function add($param)
    {
        $obj = TestClass::createByBiz($param);
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

        if ($request['tdata']) {
            $request_param[] = 'tdata='.$request['tdata'];
            $sql_condition[] = 'tdata = ?';
            $sql_param[]    = $request['tdata'];
        }
        if ($request['status']) {
            $request_param[] = 'status='.$request['status'];
            $sql_condition[] = 'status = ?';
            $sql_param[]    = $request['status'];
        }
        if ($request['tableidname']) {
            $request_param[] = 'tableidname='.$request['tableidname'];
            $sql_condition[] = 'tableidname = ?';
            $sql_param[]    = $request['tableidname'];
        }
        if ($request['name']) {
            $request_param[] = 'name='.$request['name'];
            $sql_condition[] = 'name = ?';
            $sql_param[]    = $request['name'];
        }
        return self::getDao()->getPager($request_param, $sql_condition,$sql_param , $options, $export);
    }

}
