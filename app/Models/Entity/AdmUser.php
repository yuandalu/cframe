<?php

namespace App\Models\Entity;

use App\Support\Loader;
use App\Support\Entity;

class AdmUser extends Entity
{

    const ID_OBJ = 'adm_users';

    const ACTIVE_Y = '1';
    const ACTIVE_N = '2';
    public static $ACTIVE = array(
        self::ACTIVE_Y => array('name' => '激活'),
        self::ACTIVE_N => array('name' => '冻结'),
    );

    public static function createByBiz($param)
    {
        $cls = __CLASS__;
        $obj = new $cls();
        $obj->id       = Loader::loadIdGenter()->create(self::ID_OBJ);
        $obj->name     = $param['name'];
        $obj->ename    = $param['ename'];
        $obj->depart   = $param['depart'];
        $obj->position = $param['position'];
        $obj->role     = $param['role'];
        $obj->token    = isset($param['token'])?$param['token']:'';
        $obj->status   = isset($param['status'])?$param['status']:1;
        return $obj;
    }

    public function isSuper()
    {
        if ($this->role == '超级管理员') {
            return true;
        } else {
            return false;
        }
    }
}