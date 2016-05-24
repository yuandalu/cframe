<?php

namespace App\Controllers\Admin;

use App\Models\Svc\UtlsSvc;
use App\Models\Svc\ErrorSvc;
use App\Models\Svc\AdminSvc;
use App\Models\Svc\AdmUserSvc;
use App\Ext\Browser;

class IndexController extends BaseController
{
    const PER_PAGE_NUM = 15;// 默认分页数

    static $NOT_LOGIN_ACTION  = array('login', 'dologin', 'logout');// 排除登录验证

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
        return render('index');
    }

    public function bindTokenAction()
    {
        $adminUser = loader('session')->get('adminUser');
        $user      = AdmUserSvc::getByEname($adminUser);

        $code   = $this->getRequest('code');
        $unbind = $this->getRequest('unbind');
        if (!is_null($code)) {
            $token = $this->getRequest('token');
            if (AdmUserSvc::checkToken($token)) {
                return ErrorSvc::format(ErrorSvc::ERR_EXISTS, null, '此验证码已被使用，正在刷新新的验证码...');
            }
            if (!is_numeric($code) || strlen($code) != 6) {
                return ErrorSvc::format(ErrorSvc::ERR_PARAM_TYPE, null, '验证码非法，必须为六位数字');
            }

            $TimeStamp = \App\Ext\Google2FA::get_timestamp();
            $secretkey = \App\Ext\Google2FA::base32_decode($token);
            $otp       = \App\Ext\Google2FA::oath_hotp($secretkey, $TimeStamp);
            if ($otp != $code) {
                return ErrorSvc::format(ErrorSvc::ERR_PARAM_TYPE, null, '验证码错误，如果多次失败，请校对手机时间，当前时间：'.date('Y-m-d H:i:s'));
            }
            AdmUserSvc::updateById($user->id, array('token'=>$token));
            return ErrorSvc::format(ErrorSvc::ERR_OK, null, '绑定成功');
        }
        if (!is_null($unbind)) {
            AdmUserSvc::updateById($user->id, array('token'=>''));
            UtlsSvc::goToAct('Index', 'bindToken');
        }

        $token = $user->token?:\App\Ext\Google2FA::generate_secret_key();
        $this->assign('user', $user);
        $this->assign('token', $token);
        return render('bindtoken');
    }

    public function loginAction()
    {
        $browser     = new Browser();
        $sys         = $browser->getBrowser();
        $browserlist = array("Chrome", "Safari", "iPhone", "iPad", "OS/2", "Apple");
        if ($this->getRequest('o') != 1) {
            if (!in_array($sys, $browserlist)) {
                $str = "<center><h1>警告!!</h1></center><br /><span style='font-size:14px;'>您用的不是Chrome浏览器,为了正常使用，请通过Chrome浏览器使用该系统！</span><br /><center><a href='/index/login?o=1'><span style='color:red'>继续登录</span></a>&nbsp;&nbsp;&nbsp;<a href='http://www.google.cn/chrome/intl/zh-CN/landing_chrome.html' target='_blank'><span style='color:red'></span></a></center>";
                UtlsSvc::showMsg($str, '/index/login',1000);
            }
        }

        $adminUser    = loader('session')->get('adminUser');
        $adminUserObj = AdmUserSvc::getByEname($adminUser);
        if ($adminUserObj) {
            UtlsSvc::goToAct('Index', 'index');
        }
        return render('login');
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
                UtlsSvc::showMsg('验证码错误', '/Index/login', 1.25);
            }
        } else {
            $verify = AdminSvc::verifyKey($user, $captcha);
            if ($verify !== true) {
                UtlsSvc::showMsg($verify, '/Index/login', 1.25);
            }
        }

        $r = AdminSvc::login($user, $pwd);
        if (!$r) {
            UtlsSvc::showMsg('用户名和密码不匹配,(<span style="color:red">请使用企业邮箱密码登陆</span>)', '/Index/login', 2);
        }
        if ($adminUserObj->token == '') {
            UtlsSvc::goToAct('Index', 'bindToken');
        }
        UtlsSvc::goToAct('Index', 'index');
    }

    public function logoutAction()
    {
        loader('session')->destroy('adminUser');
        UtlsSvc::goToAct('Index', 'login');
    }
}