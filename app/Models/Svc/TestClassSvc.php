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
        return self::getDao()->updateById($id, $param);
    }

    public static function getById($id = '0')
    {
        return self::getDao()->getById($id);
    }

    public static function deleteById($id)
    {
        return self::getDao()->deleteById($id);
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

        if ($request['testdatetime']) {
            $request_param[] = 'testdatetime='.$request['testdatetime'];
            $sql_condition[] = 'testdatetime = ?';
            $sql_param[]    = $request['testdatetime'];
        }
        if ($request['testdata']) {
            $request_param[] = 'testdata='.$request['testdata'];
            $sql_condition[] = 'testdata = ?';
            $sql_param[]    = $request['testdata'];
        }
        if ($request['testtime']) {
            $request_param[] = 'testtime='.$request['testtime'];
            $sql_condition[] = 'testtime = ?';
            $sql_param[]    = $request['testtime'];
        }
        if ($request['testint']) {
            $request_param[] = 'testint='.$request['testint'];
            $sql_condition[] = 'testint = ?';
            $sql_param[]    = $request['testint'];
        }
        if ($request['testtinyint']) {
            $request_param[] = 'testtinyint='.$request['testtinyint'];
            $sql_condition[] = 'testtinyint = ?';
            $sql_param[]    = $request['testtinyint'];
        }
        if ($request['testvarchar']) {
            $request_param[] = 'testvarchar='.$request['testvarchar'];
            $sql_condition[] = 'testvarchar = ?';
            $sql_param[]    = $request['testvarchar'];
        }
        if ($request['testint_table']) {
            $request_param[] = 'testint_table='.$request['testint_table'];
            $sql_condition[] = 'testint_table = ?';
            $sql_param[]    = $request['testint_table'];
        }
        return self::getDao()->getPager($request_param, $sql_condition,$sql_param , $options, $export);
    }

}
