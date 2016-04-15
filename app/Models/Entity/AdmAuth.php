<?php

namespace App\Models\Entity;

use App\Support\Loader;
use App\Support\Entity;

class AdmAuth extends Entity
{
    const ID_OBJ  = 'adm_auths';
    public static function createByBiz($param)
    {
        $cls = __CLASS__;
        $obj = new $cls();
        $obj->id = Loader::loadIdGenter()->create(self::ID_OBJ);
        $obj->ctime = date('Y-m-d H:i:s');
        $obj->utime = date('Y-m-d H:i:s');
        $obj->name = $param['name'];
        return $obj;
    }
}