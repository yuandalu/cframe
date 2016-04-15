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
        $adminUser    = loader('Sess')->get('adminUser');
        $adminUserObj = AdmUserSvc::getByEname($adminUser);
        if (!$adminUserObj) {
            $go = $this->getRequest('go');
            UtlsSvc::goToAct('index','notlogin', array('go'=>urlencode($go)));
        }
        $this->assign('adminUserObj', $adminUserObj);
    }

    public function notloginAction()
    {
        $browser     = new Browser();
        $sys         = $browser->getBrowser();
        $browserlist = array("Chrome", "Safari", "iPhone", "iPad", "OS/2", "Apple");
        if ($this->getRequest('o') != 1) {
            if (!in_array($sys, $browserlist)) {
                $str = "<center><h1>警告!!</h1></center><br /><span style='font-size:14px;'>您用的不是Chrome浏览器,为了正常使用，请通过Chrome浏览器使用该系统！</span><br /><center><a href='/index/notlogin/?o=1'><span style='color:red'>继续登录</span></a>&nbsp;&nbsp;&nbsp;<a href='http://www.google.cn/chrome/intl/zh-CN/landing_chrome.html' target='_blank'><span style='color:red'></span></a></center>";
                UtlsSvc::showMsg($str, '/index/index/',1000);
            }
        }

        $adminUser    = loader('Sess')->get('adminUser');
        $adminUserObj = AdmUserSvc::getByEname($adminUser);
        if ($adminUserObj) {
            UtlsSvc::goToAct('index', 'index');
        }
        $go = $this->getRequest('go');
        $go = $go?$go:(strpos($_SERVER['HTTP_REFERER'], 'index/login')?'/':$_SERVER['HTTP_REFERER']);
        $this->assign('go', urldecode($go));
    }

    public function noauthAction()
    {
        UtlsSvc::showMsg('没有权限','/index/notlogin/');
        exit;
    }

    public function loginAction()
    {
        //这里是后台
        $captcha     = strtolower($this->getRequest('security_code', ''));
        $session_val = strtolower(loader('Sess')->get('security_code'));
        $user        = $this->getRequest('user', '');
        $pwd         = $this->getRequest('pwd', '');
        $go          = $this->getRequest('go');
        if (UtlsSvc::inCompany() || true) {
            if ('' == $captcha || $session_val !=  $captcha   || '' == $session_val) {
                UtlsSvc::showMsg('验证码错误', '/index/index?go='.urlencode($go), 1.25);
            }
        } else {
            
        }

        $r = AdminSvc::login($user, $pwd);
        if(!$r)
        {
            UtlsSvc::showMsg('用户名和密码不匹配,(<span style="color:red">请使用新的企业邮箱密码登陆</span>)', '/index/index?go='.urlencode($go));
        }
        if ($go) {
            header("location:".$go);
            exit;
        } else {
            UtlsSvc::goToAct('index', 'index');
        }
    }

    public function logoutAction()
    {
        //这里是后台
        loader('Sess')->destroy('adminUser');
        UtlsSvc::goToAct('index', 'index');
    }
}