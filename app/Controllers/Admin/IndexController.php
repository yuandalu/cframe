<?php

namespace App\Controllers\Admin;

use App\Models\Svc\UtlsSvc;
use App\Models\Svc\AdminSvc;
use App\Models\Svc\AdmUserSvc;
use App\Ext\Browser;

class IndexController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $adminUser    = loader('session')->get('adminUser');
        $adminUserObj = AdmUserSvc::getByEname($adminUser);
        if (!$adminUserObj) {
            UtlsSvc::goToAct('Index', 'login');
        }
        $this->assign('adminUserObj', $adminUserObj);
        return view('index');
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

    public function noauthAction()
    {
        UtlsSvc::showMsg('没有权限','/Index/login/');
        exit;
    }

    public function doLoginAction()
    {
        $captcha     = strtolower($this->getRequest('security_code', ''));
        $session_val = strtolower(loader('session')->get('security_code'));
        $user        = $this->getRequest('user', '');
        $pwd         = $this->getRequest('pwd', '');
        if (UtlsSvc::inCompany() || true) {
            if ('' == $captcha || $session_val !=  $captcha   || '' == $session_val) {
                UtlsSvc::showMsg('验证码错误', '/Index/index', 1.25);
            }
        } else {
            
        }

        $r = AdminSvc::login($user, $pwd);
        if (!$r) {
            UtlsSvc::showMsg('用户名和密码不匹配,(<span style="color:red">请使用新的企业邮箱密码登陆</span>)', '/Index/index');
        }
        UtlsSvc::goToAct('Index', 'index');
    }

    public function logoutAction()
    {
        //这里是后台
        loader('session')->destroy('adminUser');
        UtlsSvc::goToAct('Index', 'index');
    }
}