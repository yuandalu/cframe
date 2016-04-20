<?php

namespace App\Models\Entity;

use App\Support\Loader;
use App\Support\Entity;

class TestClass extends Entity
{
    const ID_OBJ  = 'test_table';

    // 状态
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
        $obj->name_test = $param['name_test']?$param['name_test']:'';
        $obj->userid = $param['userid']?$param['userid']:'0';
        $obj->sort = $param['sort']?$param['sort']:'0';
        $obj->status = $param['status']?$param['status']:self::STATUS_YES;
        return $obj;

    }
}
