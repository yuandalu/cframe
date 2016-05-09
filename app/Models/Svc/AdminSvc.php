<?php

namespace App\Models\Svc;

use App\Support\Loader;
use App\Ext\SocketPOPClient;

class AdminSvc
{
    public static function getLoginUser()
    {
        $user = loader('session')->get('adminUser');
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
        $param['time'] = date('Y-m-d H:i:s');
        #warning 检查是否有这个用户
        if (true && self::staffAuth($user, $pwd)) {
            $param['result'] = '1';
            LogSvc::loginLog($param);
            loader('session')->set('adminUser', $user);
            setcookie('adminUser',$user, 0, '/');
            return true;
        } else {
            $param['result'] = '0';
            LogSvc::loginLog($param);
            loader('session')->destroy('adminUser');
            setcookie('adminUser',$user,0);
            return false;
        }
    }

    public static function staffAuth($uid, $pwd)
    {
        $o = new SocketPOPClient($uid.env('ADMIN_EMAIL_POSTFIX', 'local'), $pwd, env('ADMIN_POP_ADDRESS', 'local'), env('ADMIN_POP_PORT', 'local'));
        if ($o->popLogin()) {
            //优先用QQ企业邮箱登陆
            $r = 1;
        } else {
            $r = 0;
        }
        $o->closeHost();
        return $r;
    }

    public static function verifyKey($user, $key)
    {
        $userInfo = AdmUserSvc::getByEname($user);
        if (!isset($userInfo) || ($userInfo->token == '')) {
            return '用户名和密码不匹配';
        }
        $TimeStamp = \App\Ext\Google2FA::get_timestamp();
        $secretkey = \App\Ext\Google2FA::base32_decode($userInfo->token);
        $otp       = \App\Ext\Google2FA::oath_hotp($secretkey, $TimeStamp);

        // echo("Init key: $userInfo->token\n");
        // echo("Timestamp: $TimeStamp\n");
        // echo("One time password: $otp\n");

        $verifyCode = loader('dbcache')->get('token_code_'.$user);
        if ($verifyCode != $otp) {
            loader('dbcache')->set('token_codeuse_'.$user, 0, 315360000);
            loader('dbcache')->set('token_code_'.$user, $otp, 315360000);
        }
        $verifyNum  = loader('dbcache')->get('token_codeuse_'.$user);
        if ($verifyNum >= env('ADMIN_VERIFY_NUM', 'local')) {
            return '令牌失效，请等待下一次令牌';
        }

        // otpauth://totp/test@test.com?secret=1234567812345678
        $result = \App\Ext\Google2FA::verify_key($userInfo->token, $key, 0);
        if (!$result) {
            loader('dbcache')->set('token_code_'.$user, $otp, 315360000);
            loader('dbcache')->set('token_codeuse_'.$user, ($verifyNum + 1), 315360000);
            return '验证码错误';
        } else {
            loader('dbcache')->set('token_codeuse_'.$user, env('ADMIN_VERIFY_NUM', 'local'), 315360000);
        }
        return true;
    }
}