<?php

namespace App\Controllers\Admin;

use Elephant\Foundation\Controller;
use App\Models\Svc\AdmUserSvc;

class BaseController extends Controller
{
    public function __construct()
    {
        $controllerName = Controller::getControllerName();
        $actionName = Controller::getActionName();
        $admin_user = loader('Sess')->get('adminUser');
        $this->assign('admin_user', $admin_user);
        $adminuserobj = AdmUserSvc::getByEname($admin_user);
        if (in_array($controllerName, array('include', 'index'))) {
            return "";
        }
        if ($adminuserobj && $adminuserobj->isSuper()) {
            return "";
        } else {
            $auth = AuthsSvc::verify($controllerName, $actionName);
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