<?php

namespace App\Controllers\Front;

use App\Support\Loader;
use App\Models\Svc\UtlsSvc;
use App\Models\Svc\LogSvc;
use App\Models\Svc\ErrorSvc;
use App\Models\Svc\UserSvc;
use App\Conf\UserConf;

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
        // $cache = Loader::loadDBCache()->set('a', 'DBCache');
        // echo $cache = Loader::loadDBCache()->get('a');
        // $cache = loader('dbcache')->set('a', 't6 ');
        // echo $cache = loader('dbcache')->get('a');
        // Loader::loadSlaveRedis()->set('a', 2);
        // echo Loader::loadSlaveRedis()->get('a');
        // loader('session')->set('a', 'session');
        // echo loader('session')->get('a');
        // echo UtlsSvc::fenToYuan(1000);
        // LogSvc::writeLog('test', 'test');
        // var_dump(env('CAPTCHA_FONT_FILE', 'local'));
        $name = 'Frame';
        $this->assign('name', $name);
        // echo $name;
        // return view('index');
        $data = UserConf::getUsers();
        // return $data;
        // return 666;
        // return ErrorSvc::callback('test', ErrorSvc::ERR_OK, $data);
        return ErrorSvc::format(ErrorSvc::ERR_OK, $data);
    }

    public function authAction()
    {
        return UserSvc::info(true)->toAry();
    }
}