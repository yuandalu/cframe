<?php
class QFrameLogWriterFile 
{
    /**
     * 日志文件命名格式
     */
    private $_filename  = '{app}{logtype}.log.{logtime}';
    /**
     * 写文件时是否加锁，默认不加
     */
    private $_locking   = false;
    
    /**
     * 创建新的日志文件应该给的权限
     */
    private $_filemode  = 0777;
    
    /**
     * 创建日志文件给的权限
     */
    private $_dirmode   = 0755;
    
    /**
     * 日记格式，要记录哪些项
     */
    private $_logFormat = '%1$s %2$s %5$s';
    
    /**
     * 时间格式
     */
    private $_timeFormat= 'Y-m-d H:i:s';
    
    /**
     * 每一行日志以什么形式结尾
     */
    private $_eol       = "\n";
    
    /**
     * 日志是实时写入磁盘还是缓冲写入
     */
    private $_buffering = false; 
    
    /**
     * 日志文件保存目录
     */
    private $_logPath   = '';
    
    /**
     * 标识
     */
    private $_ident     = '';
    
    /**
     * 错误信息，当写日志出错时，保存错误信息
     */
    private $_errmsg    = '';

    /**
     * 缓冲区
     */
    private $_buffer = array();
    
    /**
     * 打开的日志文件句柄池
     */
    private $_handle = array();
    
    /**
     * @param string $logPath 日志保存目录
     * @param string $ident 标识符
     * @param array $option 可选项
     */
    function __construct($logPath, $ident='', $option = array())
    {/*{{{*/
        $this->_logPath = $logPath;
        $this->_ident = $ident;

        if (isset($option['locking']))
        {
            $this->_locking = $option['locking'];
        }

        if (!empty($option['filemode']))
        {
            $this->_filemode = $option['filemode'];
        }

        if (!empty($option['dirmode']))
        {
            $this->_dirmode = $option['dirmode'];
        }

        if (!empty($option['buffering']))
        {
            $this->_buffering = $option['buffering'];
        }

        if (!empty($option['eol']))
        {
            $this->_eol = $option['eol'];
        }
        else
        {
            $this->_eol = (strstr(PHP_OS, 'WIN')) ? "\r\n" : "\n";
        }

        if($this->_buffering)
        {
            register_shutdown_function(array(&$this, 'flush'));
        }
    }/*}}}*/

    /**
     * 关闭所有打开的日志文件句柄
     */
    function __destruct()
    {/*{{{*/
        foreach($this->_handle as $fp)
        {
            if($fp) fclose($fp);
        }
    }/*}}}*/

    /**
     * 记录日志，根据buffering 判断是直接写入，还是先收集再写入
     *
     * @param string $message
     * @param string $type
     * @return bool
     */
    public function log($message, $type)
    {/*{{{*/
        if(!is_string($message))
        {
            $message = print_r($message, true);
        }
        $message = $this->_formatMessage($message, $type);
        if($this->_buffering)
        {
            $this->_buffer[$type][] = $message;
            return true;
        }
        return $this->_writeLog($message, $type);
    }/*}}}*/

    /**
     * 保存日志文件到持久化存储
     */
    public function flush()
    {/*{{{*/
        $this->_writeLog($this->_buffer);
    }/*}}}*/

    /**
     * 获取错误信息
     *
     * @return unknown
     */
    public function getErrmsg()
    {/*{{{*/
        return $this->_errmsg;
    }/*}}}*/

    /**
     * 格式化日志信息
     *
     * @param string $message
     * @return string
     */
    private function _formatMessage($message, $type)
    {/*{{{*/
        $timestamp = date($this->_timeFormat, time());
        $ip = empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];
        return QFrameLog::format($this->_logFormat, $ip, $timestamp, $this->_ident, $type, $message);
    }/*}}}*/

    /**
     * 写日志
     *
     * @param string $message
     * @param string $type
     * @return bool
     */
    private function _writeLog($message, $type='')
    {/*{{{*/
        $filename['{app}']      = empty($this->_ident) ? '' : $this->_ident . '_';
        $filename['{logtype}']  = $type;
        $filename['{logtime}']  = date('Ymd');

        $msgContent = '';
        if(is_string($message))
        {
            $msgContent = $message . $this->_eol;
            $filename = $this->_logPath . '/' . strtr($this->_filename, $filename);
            return $this->_writeToFile($filename, $msgContent);
        }
        $result_write = true;
        foreach($message as $type => $msglist)
        {
            $msgContent = '';
            foreach($msglist as $line)
            {
                $msgContent .= $line . $this->_eol;
            }
            $filename['{logtype}'] = $type;
            $filepath = $this->_logPath . '/' . strtr($this->_filename, $filename);
            $success = $this->_writeToFile($filepath, $msgContent);
            if(false == $success)
            {
                $result_write = $success;
            }
        }
        return $result_write;
    }/*}}}*/

    /**
     * 把日志内容写到文件
     *
     * @param string $filename
     * @param string $content
     * @return bool
     */
    private function _writeToFile($filename, $content)
    {/*{{{*/
        $fp = $this->_open($filename);
        if(false == $fp)
        {
            return $fp;
        }

        if ($this->_locking)
        {
            flock($fp, LOCK_EX);
        }

        $success = (fwrite($fp, $content) !== false);

        if ($this->_locking)
        {
            flock($fp, LOCK_UN);
        }

        return $success;
    }/*}}}*/

    /**
     * 打开日志文件
     *
     * @param string $filename 日志文件完整路径
     * @return resource
     */
    private function _open($filename)
    {/*{{{*/
        $key = md5($filename);
        if(isset($this->_handle[$key]) && is_resource($this->_handle[$key]))
        {
            return $this->_handle[$key];
        }
        /* If the log file's directory doesn't exist, create it. */
        if (!is_dir(dirname($filename)))
        {
            $createDir = $this->_mkpath($filename, $this->_dirmode);
            if(false == $createDir)
            {
                return $createDir;
            }
        }

        /* Determine whether the log file needs to be created. */
        $creating = !file_exists($filename);

        /* Obtain a handle to the log file. */
        $fp = fopen($filename, 'a');
        if(false === $fp)
        {
            $this->_errmsg = 'Permission denied - failed to open file: ' . $filename ;
            return $fp;
        }

        /* Attempt to set the file's permissions if we just created it. */
        if ($creating && $fp) {
            chmod($filename, $this->_filemode);
        }

        $this->_handle[$key] = $fp;
        return $fp;
    }/*}}}*/

    /**
     * 创建日志目录
     *
     * @param string $path 日志文件路径
     * @param mixed $mode 
     * @return bool
     */
    private function _mkpath($path, $mode = 0755)
    {/*{{{*/
        /* Separate the last pathname component from the rest of the path. */
        $head = dirname($path);
        $tail = basename($path);

        /* Make sure we've split the path into two complete components. */
        if (empty($tail))
        {
            $head = dirname($path);
            $tail = basename($path);
        }

        $success = mkdir($head, $mode, true);
        if(false == $success)
        {
            $this->_errmsg = 'Permission denied - Warning: mkdir() '. $head ;
            return $success;
        }
        return $success;
    }/*}}}*/
}
