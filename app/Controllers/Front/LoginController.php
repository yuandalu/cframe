<?php

namespace App\Controllers\Front;

use App\Models\Svc\ErrorSvc;
use App\Models\Svc\UtlsSvc;
use App\Models\Svc\UserSvc;

class LoginController extends BaseController
{
    // 需要排除验证登录的action名
    static $NOT_LOGIN_ACTION  = array('index', 'login', 'reg');
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
        if (UserSvc::info()) {
            header("location:/");
        }
        $go = $this->getRequest('go', '/');
        $this->assign('go', $go);
        return view('index');
    }

    public function loginAction()
    {
        $mobile   = $this->getRequest('mobile');
        $password = $this->getRequest('password');
        $go       = $this->getRequest('go', '/');

        if (!empty($mobile) && !empty($password)) {
            $login = UserSvc::login($mobile, $password);
            if ($login['e'] == ErrorSvc::ERR_OK) {
                if (UtlsSvc::isMobile()) {
                    return ErrorSvc::format($login['e'], null, $login['m']);
                } else {
                    header("location:".$go);
                }
            } else {
                if (!UtlsSvc::isMobile()) {
                    return ErrorSvc::format($login['e'], null, $login['m']);
                } else {
                    header("location:/Login/index?error=".urlencode($login['m']));
                }
            }
        }
    }

    public function regAction()
    {
        $mobile   = $this->getRequest('mobile');
        $password = $this->getRequest('password');
        $nickname = $this->getRequest('nickname');
        $go       = $this->getRequest('go', '/');

        if (!empty($mobile) && !empty($password)) {
            $result = UserSvc::register($mobile, $password, array('nickname'=>$nickname));
            if ($result['e'] == ErrorSvc::ERR_OK) {
                UserSvc::loginCore($result['data']);
            }
            return ErrorSvc::format($result['e'], null, $result['m']);
        } else {
            return ErrorSvc::format(ErrorSvc::ERR_PARAM_EMPTY);
        }
    }

    public function logoutAction()
    {
        setcookie('F', '', -1, '/', env('DOMAIN_NAME', 'local'));
        setcookie('B', '', -1, '/', env('DOMAIN_NAME', 'local'));
        header("location:/Login/index");
    }
}