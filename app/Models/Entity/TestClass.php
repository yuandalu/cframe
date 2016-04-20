<?php

namespace App\Models\Entity;

use App\Support\Loader;
use App\Support\Entity;

class TestClass extends Entity
{
    const ID_OBJ  = 'test_class';

    // tinyint
    const TESTTINYINT_YES = '1';
    const TESTTINYINT_NO = '2';
    public static $TESTTINYINT = array(
        self::TESTTINYINT_YES => array('name' => '可用'),
        self::TESTTINYINT_NO => array('name' => '不可用'),
    );

    public static function createByBiz($param)
    {
        $cls = __CLASS__;
        $obj = new $cls();
        $obj->id = Loader::loadIdGenter()->create(self::ID_OBJ);
        $obj->ctime = date('Y-m-d H:i:s');
        $obj->utime = date('Y-m-d H:i:s');
        $obj->testdatetime = $param['testdatetime']?$param['testdatetime']:date('Y-m-d H:i:s');
        $obj->testdata = $param['testdata']?$param['testdata']:date('Y-m-d');
        $obj->testtime = $param['testtime']?$param['testtime']:date('H:i:s');
        $obj->testint = $param['testint']?$param['testint']:'0';
        $obj->testtinyint = $param['testtinyint']?$param['testtinyint']:self::TESTTINYINT_YES;
        $obj->testvarchar = $param['testvarchar']?$param['testvarchar']:'default';
        $obj->testint_table = $param['testint_table']?$param['testint_table']:'0';
        return $obj;

    }
}
