<?php

namespace App\Models\Svc;

use App\Support\Loader;

class ErrorSvc
{
    const SPR = '{SPR}';
    const SHOW_KEY = 'd6fey2qf';
    const SHOW_TIMEOUT = '60';

    const TEMPLATE_TYPE_DEFAULT       = '0';
    const TEMPLATE_TYPE_LOTTERYIFRAME = '1';
    const TEMPLATE_TYPE_LOTTERY       = '2';
    const TEMPLATE_TYPE_LOTTERYAJAX   = '3';
    const TEMPLATE_TYPE_JS            = '4';
    const TEMPLATE_TYPE_JSON          = '5';

    const ERR_OK                  = '9999';
    const ERR_SYSTEM_ERROR        = '9000';
    const ERR_LOGININFO_ERROR     = '9001';
    const ERR_AUTHINFO_ERROR      = '9002';
    const ERR_NO_LOGIN            = '9003';
    const ERR_UID                 = '9004';
    const ERR_MYSQL_GET_LOCK      = '9005';
    const ERR_BUSY                = '9007';
    const USER_CLIENT_OK          = '999';
    const COMM_CLIENT_OK          = '999';

    const ERR_PARAM_EMPTY         = '1003';
    const ERR_PARAM_TYPE          = '1004';
    const ERR_PARAM_MONEY         = '1005';
    const ERR_PARAM_UID           = '1006';
    const ERR_PARAM_INVALID       = '1007';
    const ERR_INSERT_FAIL         = '1008';

    const API_OK                      ='200';

    const IAP_OK                      ='100';
    const IAP_AUTHORIZATION_FAILED    ='101';
    const IAP_MISSING_PARAMETER       ='102';
    const IAP_DUPLICATE               ='103';
    const IAP_MYSQL_INSERT            ='104';
    const IAP_NODATA                  ='105';
    const DATE_EMPTY                  ='106';

    const INT_ERROR                   ='107';
    const NO_AUTH                     ='108';
    const NO_BANK_ID                  ='109';
    const HAVEBOUGHT_VIDEO            ='110';
    const EXPIRE_TIME_OUT             ='111';
    const AGREE_TOME                  ='112';
    const NO_AMOUNT                   ='113';

    const IAP_OKMac                   ='200';
    const BACKHAND_ERROR              ='201';
    const IAP_PARAM_NOTMATCH          ='202';
    const IAP_UNIQID_REP              ='203';
    CONST ERR_ORDERS_NOTIFY           ='204';
    const HAVEBOUGHT_OUT              ='205';


    static $MSG = array(
        self::HAVEBOUGHT_OUT                         =>'购买时间已经过期',
        self::ERR_ORDERS_NOTIFY                      =>'订单sql更新错误',
        self::IAP_UNIQID_REP                         =>'uniqid 重复',
        self::IAP_PARAM_NOTMATCH                     =>'提交的参数不匹配！伪造参数',
        self::BACKHAND_ERROR                         =>'充值后续流程处理失败！',
        self::ERR_ACCOUNT_BALANCE_FAILED             =>'支付不成功！请重新支付！',
        self::IAP_OKMac                              =>'success',
        self::NO_AMOUNT                              =>'必须填写充值金额！',
        self::AGREE_TOME                             =>'请先同意会员服务协议！',
        self::EXPIRE_TIME_OUT                        =>'有效期已过！',
        self::HAVEBOUGHT_VIDEO                       =>'已购买过了',
        self::NO_BANK_ID                             =>'请先选择银行！',
        self::NO_AUTH                                =>'该专辑不存在！',
        self::INT_ERROR                              => 'type error',
        self::DATE_EMPTY                             => 'no data',
        self::IAP_NODATA                             => 'no data',
        self::IAP_OK                                 => 'success',
        self::ERR_IAPPAY_ADD_FAIL                    => 'fail',
        self::IAP_AUTHORIZATION_FAILED               => 'authorization failed',
        self::IAP_MISSING_PARAMETER                  => 'missing parameter',
        self::IAP_DUPLICATE                          => 'duplicate',
        self::IAP_MYSQL_INSERT                       => 'mysql_insert is wrong',

        self::ERR_OK                                 => '操作成功',
        self::ERR_SYSTEM_ERROR                       => '系统错误',
        self::ERR_LOGININFO_ERROR                    => '登陆信息错误',
        self::ERR_AUTHINFO_ERROR                     => '您没有权限查看该页',
        self::ERR_NO_LOGIN                           => '账号未登录,请登录',
        self::ERR_UID                                => '请刷新页面，检查当前账号',
        self::ERR_MYSQL_GET_LOCK                     => '数据库锁失败，请重试',
        self::ERR_PARAM_EMPTY                        => '参数为空',
        self::ERR_PARAM_TYPE                         => '参数类型错误',
        self::ERR_PARAM_MONEY                        => '金额错误',
        self::ERR_PARAM_UID                          => 'qid错误',
        self::ERR_INSERT_FAIL                        => '创建失败',
        self::ERR_PARAM_INVALID                      => '参数不合法',
        self::API_OK                                 => '正常',
        self::ERR_BUSY                               => '系统繁忙，请稍后再试！',
    );


    public static function desc( $result )
    {
        if($result['e'])
        {
            $result['m'] =  self::getMsg($result['e']);
        }
        return $result;
    }

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

    public static function show( $errno , $templates_type = 1 )
    {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $templates_type==self::TEMPLATE_TYPE_JSON)
        {
            echo json_encode(array('e'=>$errno,'m'=>self::getMsg($errno)));
            exit;
        }else
        {
            if($errno == ErrorSvc::ERR_NO_LOGIN)
            {

                header('Location: http://passport.youmi.cn/login/?go='.urlencode('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']));
                exit;
            }
            UtlsSvc::goToAct( 'error', 'show', self::formatShowParam( $errno , $templates_type ) );
        }
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

    private static function formatShowParam( $e , $templates_type )
    {
        $t = time();
        $s = self::makeShowSign( $e, $t );
        return array( 'e' => $e, 't' => $t, 's' => $s , 'tpl' => $templates_type );
    }

    private static function makeShowSign( $e, $t )
    {
        return md5( $e.'|'.$t.'|'.self::SHOW_KEY );
    }

    public static function checkShowSign( $e, $t, $s )
    {
        if ( '' == $s )
        {
            return false;
        }

        if ( self::makeShowSign( $e, $t ) == $s )
        {
            return true;
        }

        return false;
    }

    public static function writeLog( $errno, $input, $log_name, $lock_key = '' )
    {
        if ( '' != $lock_key )
        {
            if(is_array($lock_key))
            {
                foreach($lock_key as $key)
                MysqlSvc::releaseLock( $key );
            }else
            {
                MysqlSvc::releaseLock( $lock_key );
            }
        }
        LogSvc::get( $log_name )->log( self::formatLogInfo( $errno, $input ) );
        return array( 'e' => $errno,'m'=>ErrorSvc::$MSG[$errno]);
    }

    public static function writeXmlLog( $errno, $input, $log_name, $lock_key = '' )
    {
        LogSvc::get( $log_name )->log( self::formatLogInfo( $errno, $input ) );
        return array( 'result_code' => $errno );
    }

    private static function formatLogInfo( $errno, $input )
    {
        $result = self::SPR.'errno='.$errno;
        if(is_array($input))
        {
            foreach ( $input as $k => $v )
            {
                if ( is_array( $v) || is_object( $v ) )
                {
                    $v = serialize( $v );
                }
                $result.= self::SPR.$k.'='.$v;
            }
        }elseif( is_object($input) )
        {
            $result = serialize($input);
        }else
        {
            $result = $input;
        }
        return $result;
    }
}
