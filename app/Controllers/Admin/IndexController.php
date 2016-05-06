<?php

namespace App\Controllers\Admin;

use App\Models\Svc\UtlsSvc;
use App\Models\Svc\AdminSvc;
use App\Models\Svc\AdmUserSvc;
use App\Ext\Browser;

class IndexController extends BaseController
{
    const PER_PAGE_NUM = 15;// 默认分页数

    static $NOT_LOGIN_ACTION  = array('login', 'dologin');// 排除登录验证

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
        return view('index');
    }

    public function qrcodeAction()
    {
        $adminUser = loader('session')->get('adminUser');
        $user      = AdmUserSvc::getByEname($adminUser);
        $token     = $user->token?$user->token:\App\Ext\Google2FA::generate_secret_key();
        $new = $this->getRequest('new', false);
        if ($new || ($user->token == '')) {
            $token = \App\Ext\Google2FA::generate_secret_key();
            AdmUserSvc::updateById($user->id, array('token'=>$token));
        }
        $str = 'otpauth://totp/'.$adminUser.'@guixue.com?secret='.$token;
        return \App\Ext\QRcode::png($str, false, 10, 5, 2, false, 0xFFFFFF, 0x2196F3);
    }

    public function loginAction()
    {
        $browser     = new Browser();
        $sys         = $browser->getBrowser();
        $browserlist = array("Chrome", "Safari", "iPhone", "iPad", "OS/2", "Apple");
        if ($this->getRequest('o') != 1) {
            if (!in_array($sys, $browserlist)) {
                $str = "<center><h1>警告!!</h1></center><br /><span style='font-size:14px;'>您用的不是Chrome浏览器,为了正常使用，请通过Chrome浏览器使用该系统！</span><br /><center><a href='/index/login/?o=1'><span style='color:red'>继续登录</span></a>&nbsp;&nbsp;&nbsp;<a href='http://www.google.cn/chrome/intl/zh-CN/landing_chrome.html' target='_blank'><span style='color:red'></span></a></center>";
                UtlsSvc::showMsg($str, '/index/index/',1000);
            }
        }

        $adminUser    = loader('session')->get('adminUser');
        $adminUserObj = AdmUserSvc::getByEname($adminUser);
        if ($adminUserObj) {
            UtlsSvc::goToAct('Index', 'index');
        }
        return view('login');
    }

    public function doLoginAction()
    {
        $captcha = strtolower($this->getRequest('security_code', ''));
        $user    = $this->getRequest('user', '');
        $pwd     = $this->getRequest('pwd', '');
        $adminUserObj = AdmUserSvc::getByEname($user);
        if ($adminUserObj && $adminUserObj->token == '') {
            $session_val = strtolower(loader('session')->get('security_code'));
            if (('' == $captcha) || ($session_val != $captcha) || ('' == $session_val)) {
                UtlsSvc::showMsg('验证码错误', '/Index/index', 1.25);
            }
        } else {
            $verify = AdminSvc::verifyKey($user, $captcha);
            if ($verify !== true) {
                UtlsSvc::showMsg($verify, '/Index/index', 1.25);
            }
        }

        $r = AdminSvc::login($user, $pwd);
        if (!$r) {
            UtlsSvc::showMsg('用户名和密码不匹配,(<span style="color:red">请使用新的企业邮箱密码登陆</span>)', '/Index/index');
        }
        if ($adminUserObj->token == '') {
            UtlsSvc::goToAct('Index', 'qrcode');
        }
        UtlsSvc::goToAct('Index', 'index');
    }

    public function logoutAction()
    {
        loader('session')->destroy('adminUser');
        UtlsSvc::goToAct('Index', 'index');
    }
}