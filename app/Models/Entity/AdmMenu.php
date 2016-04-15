<?php

namespace App\Models\Entity;

use App\Support\Loader;
use App\Support\Entity;

class AdmMenu extends Entity
{
    const ID_OBJ  = 'adm_menus';

    public static $ICON = array(
        '系统管理' => 'fa-gears',
        '权限管理' => 'fa-unlock-alt',
    );

    public static function createByBiz($param)
    {
        $cls = __CLASS__;
        $obj = new $cls();
        $obj->id = Loader::loadIdGenter()->create(self::ID_OBJ);
        $obj->ctime = date('Y-m-d H:i:s');
        $obj->utime = date('Y-m-d H:i:s');
        $obj->name = $param['name'];
        $obj->url = $param['url'];
        $obj->sort = $param['sort'];
        $obj->oneclass = $param['oneclass'];
        if ($param['groupid']=="") {
            $obj->groupid = Loader::loadIdGenter()->create("adm_menugroup");
        } else {
            $obj->groupid = $param['groupid'];
        }
        $obj->aid = $param['aid'];
        $obj->curr_menu = $param['curr_menu'];
        $obj->curr_submenu = $param['curr_submenu'];
        return $obj;
    }
}