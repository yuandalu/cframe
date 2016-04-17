<?php

namespace App\Controllers\Front;

use Elephant\Foundation\Controller;
use App\Models\Svc\UtlsSvc;
use App\Models\Svc\ErrorSvc;
use App\Models\Svc\UserSvc;

class BaseController extends Controller
{
    public function __construct($require_login = true)
    {
        //#mark用户信息变更需要更新用户的cookie，或者让用户重新登录
        $user = UserSvc::info(true);
        //用户登录认证
        if ($require_login) {
            if (!$user) {
                if (UtlsSvc::isMobile()) {
                    $result =  ErrorSvc::format(ErrorSvc::ERR_NO_LOGIN);
                    echo json_encode($result);exit;
                }
                header('Location: /Login/index?go='.urlencode('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']));
                exit;
            }
            // 检测账户是否被禁用
            // 初始化相关其他的账户信息
        }
    }
}