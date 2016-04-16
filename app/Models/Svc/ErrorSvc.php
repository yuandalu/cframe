<?php

namespace App\Models\Svc;

use App\Support\Loader;

class ErrorSvc
{
    const SPR          = '{SPR}';
    const SHOW_KEY     = 'd6fey2qf';
    const SHOW_TIMEOUT = 60;

    //通用号段区
    const ERR_OK                  = '9999';
    const ERR_SYSTEM_ERROR        = '9000';
    const ERR_LOGININFO_ERROR     = '9001';
    const ERR_AUTHINFO_ERROR      = '9002';
    const ERR_NO_LOGIN            = '9003';
    const ERR_UID                 = '9004';
    const ERR_MYSQL_GET_LOCK      = '9005';
    const ERR_BUSY                = '9006';
    const ERR_PARAM_EMPTY         = '9007';
    const ERR_PARAM_TYPE          = '9008';
    const ERR_PARAM_MONEY         = '9009';
    const ERR_PARAM_UID           = '9010';
    const ERR_PARAM_INVALID       = '9011';
    const ERR_INSERT_FAIL         = '9012';
    const ERR_UPDATE_FAIL         = '9013';
    const ERR_CAPTCHA_ERROR       = '9014';
    const ERR_IMAGE_CAPTCHA_ERROR = '9015';
    const ERR_NOT_EXISTX          = '9016';
    const ERR_MOBILE_INVALID      = '9017';
    const ERR_NOT_MODIFICATION    = '9018';
    const ERR_REGISTER_FAILED     = '9019';
    const ERR_EXISTS              = '9020';

    /******主站业务START******/

    /******主站业务STOP******/

    static $MSG = array(
        //通用
        self::ERR_OK                                 => '操作成功',
        self::ERR_SYSTEM_ERROR                       => '系统错误',
        self::ERR_LOGININFO_ERROR                    => '登陆信息错误',
        self::ERR_AUTHINFO_ERROR                     => '您没有权限查看该页',
        self::ERR_NO_LOGIN                           => '账号未登录,请登录',
        self::ERR_UID                                => '请刷新页面，检查当前账号',
        self::ERR_MYSQL_GET_LOCK                     => '数据库锁失败，请重试',
        self::ERR_BUSY                               => '系统繁忙，请稍后再试！',
        self::ERR_PARAM_EMPTY                        => '参数为空',
        self::ERR_PARAM_TYPE                         => '参数类型错误',
        self::ERR_PARAM_MONEY                        => '金额错误',
        self::ERR_PARAM_UID                          => 'qid错误',
        self::ERR_PARAM_INVALID                      => '参数不合法',
        self::ERR_INSERT_FAIL                        => '创建失败',
        self::ERR_UPDATE_FAIL                        => '更新失败',
        self::ERR_CAPTCHA_ERROR                      => '验证码错误',
        self::ERR_IMAGE_CAPTCHA_ERROR                => '图形验证码错误',
        self::ERR_NOT_EXISTX                         => '记录不存在',
        self::ERR_MOBILE_INVALID                     => '手机号码格式错误',
        self::ERR_NOT_MODIFICATION                   => '没有做任何修改',
        self::ERR_REGISTER_FAILED                    => '注册失败',
        self::ERR_EXISTS                             => '已存在',

        /******主站业务START******/

        /******主站业务STOP******/
    );

    public static function getMsg( $errno )
    {
        if ( empty( $errno ) )
        {
            return '';
        }
        if ( !array_key_exists( $errno, self::$MSG ) )
        {
            return '未知错误';
        }

        return self::$MSG[$errno];
    }

    public static function showMsg($result,$url='')
    {
        UtlsSvc::showMsg(ErrorSvc::getMsg($result['e']),$url);
    }

    /**
     * [showJson 用于JSON的输出]
     * @param  string $errno 错误号
     * @param  string $data  数据
     * @param  string $m     自定义提示信息
     * @return json        返回json
     */
    public static function showJson($errno, $data = null, $m = '')
    {
        $m = $m ? $m : self::getMsg($errno);
        $result = array(
            'e' => $errno,
            'm' => $m
        );
        if (!is_null($data)) {
            $result['data'] = $data;
        }
        echo json_encode($result);
        exit;
    }
    /**
     * [showJC 根据参数决定是否输出回调]
     * @param  string $callback 回调函数，留空则输出json
     * @param  string $errno 错误号
     * @param  string $data  数据
     * @param  string $m     自定义提示信息
     * @return array        返回数组
     */
    public static function showJC($callback, $errno, $data = null, $m = '')
    {
        $m = $m ? $m : self::getMsg($errno);
        $result = array(
            'e' => $errno,
            'm' => $m
        );
        if (!is_null($data)) {
            $result['data'] = $data;
        }
        if ($callback) {
            echo ' ' . htmlspecialchars($callback) . '(' . json_encode($result) . ')';
        } else {
            echo json_encode($result);
        }
        exit;
    }
    /**
     * [returnData 用于内部方法间的信息传递]
     * @param  string $errno 错误号
     * @param  string $data  数据
     * @param  string $m     自定义提示信息
     * @return array        返回数组
     */
    public static function returnData($errno, $data = null, $m = '')
    {
        $m = $m ? $m : self::getMsg($errno);
        $result = array(
            'e' => $errno,
            'm' => $m
        );
        if (!is_null($data)) {
            $result['data'] = $data;
        }
        return $result;
    }
    private static function formatShowParam($e, $url, $m, $time)
    {
        $t = time();
        $s = self::makeShowSign($e, $t);
        return array(
            'e'    => $e,
            't'    => $t,
            's'    => $s,
            'm'    => $m,
            'go'   => $url,
            'time' => $time
        );
    }
    private static function makeShowSign($e, $t)
    {
        return md5($e . '|' . $t . '|' . self::SHOW_KEY);
    }
    public static function checkShowSign($e, $t, $s)
    {
        if ('' == $s) {
            return false;
        }
        if (self::makeShowSign($e, $t) == $s) {
            return true;
        }
        return false;
    }
    public static function writeLog($errno, $input, $log_name, $lock_key = '')
    {
        if ('' != $lock_key) {
            if (is_array($lock_key)) {
                foreach ($lock_key as $key) MysqlSvc::releaseLock($key);
            } else {
                MysqlSvc::releaseLock($lock_key);
            }
        }
        logs($log_name)->log(self::formatLogInfo($errno, $input));
        return array(
            'e' => $errno,
            'm' => ErrorSvc::$MSG[$errno]
        );
    }

    private static function formatLogInfo($errno, $input)
    {
        $result = self::SPR . 'errno=' . $errno;
        if (is_array($input)) {
            foreach ($input as $k => $v) {
                if (is_array($v) || is_object($v)) {
                    $v = serialize($v);
                }
                $result.= self::SPR . $k . '=' . $v;
            }
        } elseif (is_object($input)) {
            $result = serialize($input);
        } else {
            $result = $input;
        }
        return $result;
    }
}
