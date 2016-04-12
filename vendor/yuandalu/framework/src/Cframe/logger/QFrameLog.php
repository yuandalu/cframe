<?php
class QFrameLog
{
    /*{{{*/
    const OUTPUT_MODE_FIREPHP = 'firephp'; //将调试信息打印在firebug控制台，需要安装firebug & firephp 插件，只支持firefox
    const OUTPUT_MODE_ECHO    = 'echo';    //直接将调试信息打印在页面上
    const OUTPUT_MODE_COMMENT = 'comment'; //将调试信息以注释形式<!-- -->打印在页面上

    const LOG_TYPE_SYS      = 'system';  //系统错误或未知的异常
    const LOG_TYPE_WARN     = 'warning'; //一些警告信息
    const LOG_TYPE_INFO     = 'info';    //一般的日志信息
    const LOG_TYPE_SQL      = 'sql';     // 执行成功的sql
    const LOG_TYPE_SQLERR   = 'sql_err'; // 出错的sql，包括语法错误及db服务的错误
    const LOG_TYPE_BIZ      = 'biz';     // 业务输入输出日志，一般用于接口服务，记录请求的post数据
    const LOG_TYPE_BIZERR   = 'biz_err'; // 业务的已知错误，也是多用于接口服务
    const LOG_TYPE_SDKTIME  = 'sdk_time';// 调用sdk的日志，一般记录调用方法，及花费时间


    private $_writer     = false;
    private $_output     = false;
    private $_outputMode = 'firephp';
    
    static public $_formatMap = array(
                            '%{ip}'         => '%1$s',
                            '%{timestamp}'  => '%2$s',
                            '%{ident}'      => '%3$s',
                            '%{type}'       => '%4$s',
                            '%{message}'    => '%5$s',
                            '%\{'           => '%%{');
    /*}}}*/

    function __construct($writer)
    {/*{{{*/
        //这个writer 只是文件模式        
        $this->_writer = $writer;
    }/*}}}*/

    /**
     *
     * @param string $logPath 日志保存路径
     * @param string $fileMod 生成log文件的权限
     * @param string $app 项目名，以备日志集中存储
     * @param array $option 其它一些附加项
     * @return object
     */
    static public function getInstance($logPath, $fileMod=0777, $app='', $option=array())
    {/*{{{*/
        static $obj;
        if($obj instanceof self)
        {
            return $obj;
        }
        $option['filemode'] = $fileMod;
        $writer = new QFrameLogWriterFile($logPath, $app, $option);
        $obj = new self($writer);
        return $obj;
    }/*}}}*/

    /**
     * @param mixed $msg 要记录的日志信息，字符串或数组或对象
     * @return bool
     */
    public function sys($msg)
    {/*{{{*/
        return $this->_writeLog($msg, self::LOG_TYPE_SYS);
    }/*}}}*/

    public function warn($msg)
    {/*{{{*/
        return $this->_writeLog($msg, self::LOG_TYPE_WARN);
    }/*}}}*/

    public function info($msg)
    {/*{{{*/
        return $this->_writeLog($msg, self::LOG_TYPE_INFO);
    }/*}}}*/

    /**
     * @param string $sql 
     * @param array $values bind values
     */
    public function sql($sql, $values=array())
    {/*{{{*/
        $msg['sql'] = $sql;
        if(!empty($values))
        {
            $msg['values'] = $values;
        }

        return $this->_writeLog($msg, self::LOG_TYPE_SQL);
    }/*}}}*/

    /**
     * @param mixed $msg 错误信息
     */
    public function sqlerr($errmsg, $sql, $values=array())
    {/*{{{*/
        $msg['errmsg'] = $errmsg;
        $msg['sql'] = $sql;
        if(!empty($values))
        {
            $msg['values'] = $values;
        }
        return $this->_writeLog($msg, self::LOG_TYPE_SQLERR);
    }/*}}}*/

    public function biz($msg)
    {/*{{{*/
        return $this->_writeLog($msg, self::LOG_TYPE_BIZ);
    }/*}}}*/

    public function bizerr($msg)
    {/*{{{*/
        return $this->_writeLog($msg, self::LOG_TYPE_BIZERR);
    }/*}}}*/

    /**
     * @param mixed $msg sdk日志一般包括调用的方法，花费的时间等
     */
    public function sdktime($msg)
    {/*{{{*/
        return $this->_writeLog($msg, self::LOG_TYPE_SDKTIME);
    }/*}}}*/

    /**
     *当logType = memcache时，会在logPath下生成 $app_$logType.log.date
     * 
     * @param mixed $msg
     * @param string $logType log的类别，这个会决定log文件名
     */
    public function log($msg, $logType)
    {/*{{{*/
        return $this->_writeLog($msg, $logType);
    }/*}}}*/

    /**
     * 与openOutput的区别是，使用此方法普通的日志不会被显示出来，而日志也不会被记录到文件
     *
     * @param mixed $msg 调试的信息
     * @param string $viewType echo 或 firephp
     */
    public function setOutput($msg, $type=self::LOG_TYPE_INFO, $encoding='gbk')
    {/*{{{*/
        $writer = $this->_getOutputWriter($this->_outputMode);
        if($this->_outputMode == self::OUTPUT_MODE_FIREPHP && $encoding != 'utf8')
        {
            $msg = $this->_convertEncoding($msg, 'utf-8', $encoding);
        }
        $writer->log($msg, $type);
    }/*}}}*/

    /**
     * 打开日志输出，打开后日志在记到文件的同时，会在页面执行完成后将以OUTPUT_MODE形式显示出来
     */
    public function openOutput($outputMode=self::OUTPUT_MODE_FIREPHP)
    {/*{{{*/
        $this->_outputMode = $outputMode;
        $this->_output = true;
    }/*}}}*/

    /**
     * 设置调试信息显示方式
     *
     * @param string $viewType 显示方式
     */
    public function setOutputMode($outputMode=self::OUTPUT_MODE_FIREPHP)
    {/*{{{*/
        $this->_outputMode = $outputMode;
    }/*}}}*/

    /**
     * 调用writer的log方法来保存日志信息
     *
     * @param string $msg 日志内容
     * @param string $type 日志分类
     * @return array
     */
    private function _writeLog($msg, $type)
    {/*{{{*/
        if($this->_output)
        {
            $this->setOutput($msg, $type);
        }
        $ret['errno'] = 0;
        $result_write = $this->_writer->log($msg, $type);
        if(false == $result_write)
        {
            $ret['errno'] = 1;
            $ret['errmsg'] = $this->_writer->getErrmsg();
        }
        return $ret;
    }/*}}}*/


    /**
     * 获取示志显示的实例
     *
     * @param string $outputMode
     * @return object
     */
    private function _getOutputWriter($outputMode=self::OUTPUT_MODE_FIREPHP)
    {/*{{{*/
        static $outputWriters;
        if(isset($outputWriters[$outputMode]))
        {
            return $outputWriters[$outputMode];
        }
        if($outputMode == self::OUTPUT_MODE_FIREPHP )
        {
            $writer = new QFrameLogWriterFirephp();
        }
        else
        {
            $writer = new QFrameLogWriterDisplay($outputMode);
        }
        $outputWriters[$outputMode] = $writer;
        return $writer;
    }/*}}}*/

    /**
     * 输入换输入的内容的编码, firephp只显示utf8字符串
     *
     * @param mixed $arr
     * @param string $toEncoding
     * @param string $fromEncoding
     * @param bool $convertKey
     * @return mixed
     */
    function _convertEncoding($arr, $toEncoding, $fromEncoding)
    {/*{{{*/
        if ($toEncoding == $fromEncoding) return $arr;
        if (is_array($arr))
        {
            foreach($arr as $key => $value)
            {
                if (is_array($value))
                {
                    $value = $this->_convertEncoding($value, $toEncoding, $fromEncoding);
                }
                else
                {
                    $value = mb_convert_encoding($value, $toEncoding, $fromEncoding);
                }
                $arr[$key] = $value;
            }
        }
        else
        {
            $arr = mb_convert_encoding($arr, $toEncoding, $fromEncoding);
        }
        return $arr;
    }/*}}}*/
   
    static function format($format, $ip, $timestamp, $ident, $type, $message)
    {/*{{{*/
        return sprintf($format,
                       $ip,
                       $timestamp,
                       $ident =='' ? '-' : $ident,
                       $type == '' ? '-' : $type,
                       $message);
    }/*}}}*/
}
