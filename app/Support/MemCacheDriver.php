<?php

namespace App\Support;

class MemCacheDriver
{
    const FLAG_NO  = false;
    const FLAG_YES = true;
    const NON_EXPIRE = 0;

    private $_ins  = null;
    private $_flag = null;

    public function __construct($servers, $flag = self::FLAG_NO)
    {
        $this->_ins = new \Memcached();
        $this->_ins->setOption(\Memcached::OPT_COMPRESSION, false);
        $this->_ins->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
    
        foreach ($servers as $s)
        {
            if($s['user'])
            {
                $this->_ins->setSaslAuthData($s['user'], $s['password']);
            }
            
            $this->_ins->addServer($s['host'], $s['port']);
        }
        $this->_flag = $flag;
    }

    public function get($key)
    {
        return $this->_ins->get($key);
    }

    public function set($key, $value, $expire = self::NON_EXPIRE)
    {
        return $this->_ins->set($key, $value,  $expire);
    }

    public function add($key, $value, $expire = self::NON_EXPIRE)
    {
        return $this->_ins->add($key, $value,  $expire);
    }

    public function delete($key)
    {
        return $this->_ins->delete($key);
    }

    public function flush()
    {
        return $this->_ins->flush();
    }

    public function increment($key, $value = 1)
    {
        return $this->_ins->increment($key, $value);
    }

    public function decrement($key, $value = 1)
    {
        return $this->_ins->decrement($key, $value);
    }

    public function update($key, $value)
    {
        $value = (int) $value;
        if ($value > 0)
        {
            return $this->increment($key, $value);
        }
        return $this->decrement($key, abs($value));
    }

    public function close()
    {
        return $this->_ins->quit();
    }

    public function __destruct()
    {
        $this->close();
    }
}