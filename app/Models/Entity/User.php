<?php

namespace App\Models\Entity;

use App\Support\Loader;
use App\Support\Entity;

class User extends Entity
{
    const ID_OBJ  = 'users';

    // 状态
    const STATUS_Y = '1';
    const STATUS_N = '2';
    public static $STATUS = array(
        self::STATUS_Y => array('name' => '正常'),
        self::STATUS_N => array('name' => '禁用'),
    );

    public static function createByBiz($param)
    {
        $cls = __CLASS__;
        $obj = new $cls();
        $obj->id = Loader::loadIdGenter()->create(self::ID_OBJ);
        $obj->ctime = date('Y-m-d H:i:s');
        $obj->utime = date('Y-m-d H:i:s');
        $obj->mobile = $param['mobile']?$param['mobile']:'';
        $obj->nickname = $param['nickname']?$param['nickname']:'';
        $obj->password = $param['password']?$param['password']:'';
        $obj->salt = $param['salt']?$param['salt']:'';
        $obj->status = $param['status']?$param['status']:self::STATUS_Y;
        return $obj;

    }
}
