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
        if (in_array($controllerName, array('include', 'index'))) {
            return "";
        }
        $adminUser    = loader('Sess')->get('adminUser');
        $adminUserObj = AdmUserSvc::getByEname($adminUser);
        if (!$adminUserObj) {
            UtlsSvc::goToAct("index", "notlogin");
        }
        $this->assign('adminUser', $adminUser);
        $this->assign('adminUserObj', $adminUserObj);
        if ($adminUserObj && $adminUserObj->isSuper()) {
            return "";
        } else {
            $auth = AdmAuthSvc::verify($controllerName, $actionName);
            if ($auth == "fail") {
                if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
                    UtlsSvc::showMsg('您无此权限', '/index/index/');
                    exit;
                } else {
                    UtlsSvc::showMsg('您无此权限', '/index/index/');
                }
            } else {
                return "";
            }
        }
    }
}