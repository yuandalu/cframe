<?php

namespace App\Models\Svc;

use App\Support\Loader;

class AdminSvc
{
    public static function getLoginUser()
    {
        $user = loader('Sess')->get('adminUser');
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    public static function login($user, $pwd)
    {
        $user = trim($user," ");
        $ip = UtlsSvc::getClientIP();
        $param['ip'] = $ip;
        $param['username'] = $user;
        $param['time'] = date('Y-m-d H:i:s',time());
        if (self::checkUserAuth($user, Auth::AUTH_LOGIN) && self::staffAuth($user,$pwd)) {
            $param['result'] = '1';
            LogSvc::loginLog($param);
            loader('Sess')->set('adminUser', $user);
            setcookie('adminUser',$user, 0, '/');
            return true;
        } else {
            $param['result'] = '0';
            LogSvc::loginLog($param);
            loader('Sess')->destroy('adminUser');
            setcookie('adminUser',$user,0);
            return false;
        }
    }

    public function staffAuth($uid,$pwd)
    {
        $o = new SocketPOPClient($uid.'@yuandalu.com', $pwd, 'pop.qq.com', '110');
        if ($o->popLogin()) {
            //优先用QQ企业邮箱登陆
            $r = 1;
        } else {
            $r = 0;

        }
        $o->closeHost();
        return $r;
    }
}