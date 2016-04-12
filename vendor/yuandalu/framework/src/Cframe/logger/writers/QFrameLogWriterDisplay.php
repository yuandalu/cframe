<?php
class QFrameLogWriterDisplay
{
    /**
     * 日志显示内容
     */
    public $_logFormat = '[%2$s] - [%4$s] - %5$s';
    
    /**
     * 时间显示格式
     */
    public $_timeFormat = 'Y-m-d H:i:s';
    
    /**
     * 日志最终以什么形式显示在页面上 echo || comment
     */
    public $_displayType = 'echo';
    
    /**
     * 缓冲池
     */
    private $_buffer  = array();

    /**
     * 构造函数
     *
     * @param string $displayType 显示方法
     * @param array $option 其它一些配置选项
     */
    function __construct($displayType='echo', $option=array())
    {/*{{{*/
        $this->_displayType = $displayType;
        if (!empty($option['logFormat']))
        {
            $this->_logFormat = $option['logFormat'];
        }
        
        if($this->_displayType == 'comment')
        {
            $this->_logFormat .= "\n";
        }
        else
        {
            $this->_logFormat .= "<br>\n";
        }

        /* The user can also change the time format. */
        if (!empty($option['timeFormat']))
        {
            $this->_timeFormat = $option['timeFormat'];
        }

        register_shutdown_function(array(&$this, 'display'));
    }/*}}}*/

    /**
     * 记录日志
     *
     * @param mixed $message 日志信息
     * @param string $type 类别
     * @return bool
     */
    function log($message, $type)
    {/*{{{*/
        if(!is_string($message))
        {
            $message = print_r($message, true);
        }
        $timestamp = date($this->_timeFormat);
        $ip = empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];
        $this->_buffer[] = QFrameLog::format($this->_logFormat, $ip, $timestamp, '', $type, $message);
        return true;
    }/*}}}*/

    /**
     * 显示日志
     */
    function display()
    {/*{{{*/
        $message = implode('', $this->_buffer);
        if($this->_displayType == 'comment')
        {
            $message = "\n<!-- \n\n{$message}\n -->\n";
        }
        echo $message;
    }/*}}}*/

}
