<?php

namespace App\Controllers\Admin;

use Elephant\Foundation\Controller;
use App\Models\Svc\UtlsSvc;
use App\Models\Svc\AdmUserSvc;
use App\Models\Svc\AdmAuthSvc;

class BaseController extends Controller
{
    public function __construct()
    {
        $controllerName = Controller::getControllerName();
        $actionName     = Controller::getActionName();
        if (in_array($controllerName, array('Include', 'Index', 'include', 'index'))) {
            return "";
        }
        $adminUser    = loader('session')->get('adminUser');
        $adminUserObj = AdmUserSvc::getByEname($adminUser);
        if (!$adminUserObj) {
            UtlsSvc::goToAct("Index", "login");
        }
        $this->assign('adminUser', $adminUser);
        $this->assign('adminUserObj', $adminUserObj);
        $mastVerify = false;// #warning这里添加必须验证的逻辑
        if (!$mastVerify && $adminUserObj && $adminUserObj->isSuper()) {
            return "";
        } else {
            $auth = AdmAuthSvc::verify($controllerName, $actionName);
            if ($auth == "fail") {
                UtlsSvc::showMsg('您无此权限', '/Index/index/');
            } else {
                return "";
            }
        }
    }
}