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
            return '令牌未绑定';
        }
        $TimeStamp    = \App\Ext\Google2FA::get_timestamp();
        $secretkey    = \App\Ext\Google2FA::base32_decode($userInfo->token);
        $otp          = \App\Ext\Google2FA::oath_hotp($secretkey, $TimeStamp);

        // echo("Init key: $userInfo->token\n");
        // echo("Timestamp: $TimeStamp\n");
        // echo("One time password: $otp\n");

        $verifyCode = loader('session')->get('token_verify_code');
        if ($verifyCode != $otp) {
            loader('session')->set('token_verify_num', 0);
            loader('session')->set('token_verify_code', $otp);
        }
        $verifyNum  = loader('session')->get('token_verify_num');
        if ($verifyNum >= env('ADMIN_VERIFY_NUM', 'local')) {
            return '超过验证次数，请等待下一次令牌';
        }

        // otpauth://totp/test@test.com?secret=1234567812345678
        $result = \App\Ext\Google2FA::verify_key($userInfo->token, $key, 0);
        if (!$result) {
            loader('session')->set('token_verify_code', $otp);
            loader('session')->set('token_verify_num', ($verifyNum + 1));
            return '验证码错误';
        } else {
            loader('session')->set('token_verify_num', env('ADMIN_VERIFY_NUM', 'local'));
        }
        return true;
    }
}