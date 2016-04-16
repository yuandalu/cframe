<?php

namespace App\Controllers\Front;

use App\Support\Loader;
use App\Models\Svc\UtlsSvc;
use App\Models\Svc\LogSvc;

class IndexController extends BaseController
{
    // 需要排除验证登录的action名
    static $NOT_LOGIN_ACTION  = array('index');
    // 默认分页数
    const PER_PAGE_NUM = 15;

    public function __construct()
    {
        $isLogin  = true;
        if (in_array(strtolower($this->getActionName()), self::$NOT_LOGIN_ACTION)) {
            $isLogin = false;
        }
        parent::__construct($isLogin);
    }

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
        // var_dump(env('CAPTCHA_FONT_FILE', 'local'));
        $name = 'Frame';
        $this->assign('name', $name);
    }
}