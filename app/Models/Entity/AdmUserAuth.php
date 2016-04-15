<?php

namespace App\Models\Entity;

use App\Support\Loader;
use App\Support\Entity;

class AdmUserAuth extends Entity
{
	const ID_OBJ  = 'adm_userauth';
	public static function createByBiz($param)
	{
		$cls = __CLASS__;
		$obj = new $cls();
		$obj->id = Loader::loadIdGenter()->create(self::ID_OBJ);
		$obj->ctime = date('Y-m-d H:i:s');
		$obj->utime = date('Y-m-d H:i:s');
		$obj->user = $param['user'];
		$obj->uid = $param['uid'];
		$obj->aid = $param['aid'];
		return $obj;

	}
}