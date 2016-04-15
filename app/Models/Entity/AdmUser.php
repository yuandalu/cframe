<?php

namespace App\Models\Entity;

use App\Support\Loader;
use App\Support\Entity;

class AdmUser extends Entity
{
   
    const ID_OBJ = 'adm_users';
    
    public static function createByBiz($param)
    {
        $cls = __CLASS__;
        $obj = new $cls();
        $obj->id               =Loader::loadIdGenter()->create(self::ID_OBJ);
        $obj->name            = $param['name'];
        $obj->ename           = $param['ename'];
        $obj->depart          = $param['depart'];
        $obj->position        = $param['position'];
        $obj->role            = $param['role'];
        $obj->status          = $param['status']?$param['status']:1;
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