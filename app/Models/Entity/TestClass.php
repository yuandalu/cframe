<?php

namespace App\Models\Entity;

use App\Support\Loader;
use App\Support\Entity;

class TestClass extends Entity
{
    const ID_OBJ  = 'test_table';

    // status
    const STATUS_YES = '1';
    const STATUS_NO = '2';
    public static $STATUS = array(
        self::STATUS_YES => array('name' => '可用'),
        self::STATUS_NO => array('name' => '不可用'),
    );

    public static function createByBiz($param)
    {
        $cls = __CLASS__;
        $obj = new $cls();
        $obj->id = Loader::loadIdGenter()->create(self::ID_OBJ);
        $obj->ctime = date('Y-m-d H:i:s');
        $obj->utime = date('Y-m-d H:i:s');
        $obj->tdata = $param['tdata']?$param['tdata']:'';
        $obj->status = $param['status']?$param['status']:self::STATUS_YES;
        $obj->tableidname = $param['tableidname']?$param['tableidname']:'0';
        $obj->name = $param['name']?$param['name']:'';
        return $obj;

    }
}
