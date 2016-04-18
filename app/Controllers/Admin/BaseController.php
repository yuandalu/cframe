<?php

namespace App\Controllers\Admin;

use Elephant\Foundation\Controller;
use App\Models\Svc\UtlsSvc;
use App\Models\Svc\AdmUserSvc;
use App\Models\Svc\AdmAuthSvc;

class BaseController extends Controller
{
    public function __construct($require_login = true, $mustVerify = array())
    {
        if ($require_login) {
            $controllerName = Controller::getControllerName();
            $actionName     = Controller::getActionName();
            $adminUser      = loader('session')->get('adminUser');
            $adminUserObj   = AdmUserSvc::getByEname($adminUser);
            if (!$adminUserObj) {
                UtlsSvc::goToAct("Index", "login");
            }
            $this->assign('adminUser', $adminUser);
            $this->assign('adminUserObj', $adminUserObj);
            if (!in_array(strtolower($actionName), $mustVerify) && $adminUserObj && $adminUserObj->isSuper()) {
                return true;
            } else {
                $auth = AdmAuthSvc::verify($controllerName, $actionName);
                if ($auth == "fail") {
                    UtlsSvc::showMsg('您无此权限', '/Index/index/');
                } else {
                    return true;
                }
            }
        }
    }
}