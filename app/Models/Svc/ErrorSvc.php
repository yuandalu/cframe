<?php

namespace App\Models\Svc;

use App\Support\Loader;

class ErrorSvc
{/*{{{*/
    const SPR = '{SPR}';
    const SHOW_KEY = 'd4fc3d9f';
    const SHOW_TIMEOUT = '60';

	const TEMPLATE_TYPE_JSON		  = '0';

    const ERR_OK                  = '9999';
	const ERR_SYSTEM_ERROR        = '9000';
	const ERR_LOGININFO_ERROR     = '9001';
	const ERR_AUTHINFO_ERROR      = '9002';
    const ERR_NO_LOGIN            = '9003';
    const ERR_UID                 = '9004';
    const ERR_MYSQL_GET_LOCK      = '9005';
	const ERR_BUSY				  = '9007';

	const ERR_PARAM_EMPTY		= '9101';
	const ERR_PARAM_TYPE		= '9102';
	const ERR_PARAM_INVALID		= '9103';

	const ERR_USER_EXISTS		= '1001';



    static $MSG = array(

		self::ERR_OK                                 => '操作成功',
		self::ERR_SYSTEM_ERROR                       => '系统错误',
        self::ERR_LOGININFO_ERROR                    => '登陆信息错误',
		self::ERR_AUTHINFO_ERROR                     => '您没有权限查看该页',
        self::ERR_NO_LOGIN                           => '账号未登录,请登录',
        self::ERR_UID                                => '请刷新页面，检查当前账号',
		self::ERR_MYSQL_GET_LOCK                     => '数据库锁失败，请重试',
        self::ERR_PARAM_EMPTY                        => '参数为空',
        self::ERR_PARAM_TYPE                         => '参数类型错误',
        self::ERR_PARAM_INVALID                      => '参数不合法',
		self::ERR_BUSY								 => '系统繁忙，请稍后再试！',
		/*1100*/

		/*1101*/
		self::ERR_USER_EXISTS		=> '用户已存在，请更换一个再试',



    );
	static public function desc( $result )
	{/*{{{*/
		if($result['e'])
		{
			$result['m'] =  self::getMsg($result['e']);
		}
		return $result;
	}/*}}}*/

    static public function getMsg( $errno )
    {/*{{{*/
        if ( empty( $errno ) )
        {
            return '';
        }
        if ( !array_key_exists( $errno, self::$MSG ) )
        {
            return '未知错误';
        }

        return self::$MSG[$errno];
    }/*}}}*/

	static public function showMsg($result,$url='')
	{
		UtlsSvc::showMsg(ErrorSvc::getMsg($result['e']),$url);
	}

    static public function show( $errno , $templates_type = 0 )
    {/*{{{*/
		echo json_encode(array('e'=>$errno,'m'=>self::getMsg($errno)));
		exit;
    }/*}}}*/


    static public function writeLog( $errno, $input, $log_name, $lock_key = '' )
    {/*{{{*/
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
    }/*}}}*/

    static public function writeXmlLog( $errno, $input, $log_name, $lock_key = '' )
    {/*{{{*/
        LogSvc::get( $log_name )->log( self::formatLogInfo( $errno, $input ) );
        return array( 'result_code' => $errno );
    }/*}}}*/

    static private function formatLogInfo( $errno, $input )
    {/*{{{*/
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
    }/*}}}*/
}/*}}}*/
?>
