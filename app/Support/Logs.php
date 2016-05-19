<?php

namespace App\Support;

class Logs
{
    private $_fp = null;
    private $_file_name = '';

    public function __construct($fname)
    {
        $this->_file_name = str_replace('//','/',$fname);
    }

    public function log($msg)
    {
        if (is_null($this->_fp)) {
            $dir = dirname($this->_file_name);
            if (!is_dir($dir)) {
                mkdir($dir,0755,true);
            }
            $this->_fp = fopen($this->_file_name, 'a');
        }
        $remote_addr = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
        $msg = '['.date('Y-m-d H:i:s').' '.$this->_getClientIP().' '.$remote_addr.'] '.$msg."\n";
        if ($this->_fp) {
            fwrite($this->_fp, $msg);
        }
    }

    private function _getClientIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
        }
        return $ip;
    }

    public function __destruct()
    {
        if (is_resource($this->_fp)) {
            fclose($this->_fp);
        }
    }
}