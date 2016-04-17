<?php

namespace App\Models\Svc;

use App\Support\Loader;
use App\Models\Entity\User;


class UserSvc
{
    const OBJ = 'User';
    const PRIVATE_KEY_FILE = "resources/data/private_key.pem";
    const PUBLIC_KEY_FILE  = "resources/data/public_key.pem";
    const LOCK_REGISTER    = 'lock_register';

    public static function add($param)
    {
        $obj = User::createByBiz($param);
        return self::getDao()->add($obj);
    }

    public static function updateById($id, $param)
    {
        return self::getDao()->updateById($id, $param);
    }

    public static function getById($id = '0')
    {
        return self::getDao()->getById($id);
    }

    public static function getByMobile($mobile)
    {
        return self::getDao()->where('mobile', $mobile)->find();
    }

    public static function deleteById($id)
    {
        return self::getDao()->deleteById($id);
    }

    public static function getAll()
    {
        return self::getDao()->gets();
    }

    private static function getDao()
    {
        return Loader::loadDao(self::OBJ);
    }

    public static function info($obj = false)
    {
        $str = self::decodeRSA($_COOKIE['F']);
        $user = array();
        parse_str($str, $user);
        if ($user) {
            if ($obj) {
                return new User($user);
            }
            return $user;
        } else {
            return null;
        }
    }

    public static function login($mobile, $password, $expire = 0)
    {
        $expire = $expire?$expire+time():315360000+time();
        $user   = self::getByMobile($mobile);
        $verify = self::validatePassword($user, $password);
        if ($verify !== false) {
            if ($user['status'] == User::STATUS_N) {
                return ErrorSvc::returnData(ErrorSvc::ERR_LOGININFO_ERROR, '', '用户已经被限制登录');
            }
            return self::loginCore($user);
        } else {
            setcookie('F', '', -1, '/', env('DOMAIN_NAME', 'local'));
            setcookie('B', '', -1, '/', env('DOMAIN_NAME', 'local'));
            return ErrorSvc::returnData(ErrorSvc::ERR_LOGININFO_ERROR, '', '账户名或密码错误，请重试');
        }
    }

    public static function loginCore($user)
    {
        $cookie = self::createCookie($user);
        foreach ($cookie as $key=>$val) {
            setcookie($key, $val, $expire, '/', env('DOMAIN_NAME', 'local'));
        }
        return ErrorSvc::returnData(ErrorSvc::ERR_OK, '', '登陆成功');
    }

    public static function register($mobile, $password, $param)
    {
        $lock = self::LOCK_REGISTER;
        MysqlSvc::getLock($lock);

        if (UserSvc::getByMobile($mobile)) {
            MysqlSvc::releaseLock($lock);
            return ErrorSvc::returnData(ErrorSvc::ERR_EXISTS);
        }
        $salt     = substr(md5(uniqid().microtime(true)),rand(0,25),6);
        $password = md5($password.$salt);

        $registerParam = array(
            'mobile'   => $mobile,
            'password' => $password,
            'salt'     => $salt,
            'nickname' => $param['nickname'],
            'status'   => User::STATUS_Y,
        );
        $obj = User::createByBiz($registerParam);

        Loader::loadExecutor()->beginTrans();
        $user = self::getDao()->add($obj);
        if ($user) {
            Loader::loadExecutor()->commit();
            MysqlSvc::releaseLock($lock);
            //记录注册动作
            return ErrorSvc::returnData(ErrorSvc::ERR_OK, $user);
        }

        Loader::loadExecutor()->rollback();
        MysqlSvc::releaseLock($lock);
        return ErrorSvc::returnData(ErrorSvc::ERR_REGISTER_FAILED);
    }

    public static function validatePassword($user, $password)
    {
        if ($user) {
            return (md5($password.$user['salt']) === $user['password']);
        }
        return false;
    }

    private static function createCookie($user)
    {
        if (is_object($user)) {
            $user = $user->toAry();
        }

        $userCookie = array(
            "ID"    => $user['id'],
            "NN"    => $user['nickname'],
        );
        $logintime = time();
        $secCookie = array(
            "logintime" => $logintime,
            "id"        => $user['id'],
            "nickname"  => $user['nickname'],
        );

        $setCookie = array();
        $setCookie['B'] = http_build_query($userCookie);
        $setCookie['F'] = self::encodeRSA(http_build_query($secCookie));
        return $setCookie;
    }

    private static function encodeRSA($data)
    {
        if (!is_string($data)) {
            return null;
        }
        $private_key = openssl_pkey_get_private(file_get_contents(BASE_DIR.'/'.self::PRIVATE_KEY_FILE));
        $r = openssl_private_encrypt($data, $encrypted, $private_key, OPENSSL_PKCS1_PADDING);
        if ($r) {
            return base64_encode($encrypted);
        }
        return null;
    }

    private static function decodeRSA($crypted)
    {
        if (!is_string($crypted)) {
            return false;
        }
        $public_key = openssl_pkey_get_public(file_get_contents(BASE_DIR.'/'. self::PUBLIC_KEY_FILE));
        $crypted = base64_decode($crypted);
        $r = openssl_public_decrypt($crypted, $decrypted, $public_key);
        if($r){
            return $decrypted;
        }
        return false;
    }

    public static function lists($request=array(), $options=array(), $export = false)
    {
        $request_param = array();
        $sql_condition = array();

        if (isset($request['id']) && $request['id'] > 0) {
            $request_param[] = 'id='.$request['id'];
            $sql_condition[] = 'id = ?';
            $sql_param[]     = $request['id'];
        }

        if ($request['startdate'] != '') {
            $request_param[] = 'startdate='.$request['startdate'];
            $sql_condition[] = 'ctime >= ?';
            if ('10' >= strlen($request['startdate'])) {
                $sql_param[] = $request['startdate'].' 00:00:00';
            } else {
                $sql_param[] = $request['startdate'];
            }
        }
        if ($request['enddate'] != '') {
            $request_param[] = 'enddate='.$request['enddate'];
            $sql_condition[] = 'ctime <= ?';
            if ('10' >= strlen($request['enddate'])) {
                $sql_param[] = $request['enddate'].' 23:59:59';
            } else {
                $sql_param[] = $request['enddate'];
            }
        }

        if ($options['orderby']) {
            $request_param[] = 'orderby='.urlencode($options['orderby']);
        }

        if ($request['mobile']) {
            $request_param[] = 'mobile='.$request['mobile'];
            $sql_condition[] = 'mobile = ?';
            $sql_param[]    = $request['mobile'];
        }
        if ($request['nickname']) {
            $request_param[] = 'nickname='.$request['nickname'];
            $sql_condition[] = 'nickname = ?';
            $sql_param[]    = $request['nickname'];
        }
        if ($request['password']) {
            $request_param[] = 'password='.$request['password'];
            $sql_condition[] = 'password = ?';
            $sql_param[]    = $request['password'];
        }
        if ($request['salt']) {
            $request_param[] = 'salt='.$request['salt'];
            $sql_condition[] = 'salt = ?';
            $sql_param[]    = $request['salt'];
        }
        if ($request['status']) {
            $request_param[] = 'status='.$request['status'];
            $sql_condition[] = 'status = ?';
            $sql_param[]    = $request['status'];
        }
        return self::getDao()->getPager($request_param, $sql_condition,$sql_param , $options, $export);
    }

}
