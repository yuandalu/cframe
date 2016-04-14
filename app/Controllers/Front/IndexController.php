<?php

namespace App\Controllers\Front;

use App\Controllers\Front\BaseController;
use App\Support\Loader;
use App\Models\Svc\UtlsSvc;
use App\Models\Svc\LogSvc;

class IndexController extends BaseController
{
    public function indexAction()
    {
        // $cache = Loader::loadDBCache()->set('a', 'ttt ');
        // $cache = Loader::loadDBCache()->get('a');
        // $cache = loader('DBCache')->set('a', 'ttt ');
        // $cache = loader('DBCache')->get('a');
        // Loader::loadSlaveRedis()->set('a', 2);
        // echo Loader::loadSlaveRedis()->get('a');
        // loader('Sess')->set('a', 1);
        // echo loader('Sess')->get('a');
        // echo UtlsSvc::fenToYuan(1000);
        // LogSvc::writeLog('test', 'test');
        $this->assign('name', $cache);
    }
}