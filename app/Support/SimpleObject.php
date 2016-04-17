<?php

namespace App\Support;

class SimpleObject
{
    private $_attr = array();

    public function __construct($attr = array())
    {
        if (!is_array($attr)) {
            return;
        }
        $this->_attr = $attr;
    }

    public function __get($key)
    {
        if ($this->have($key)) {
            return $this->_attr[$key];
        }
        return '';
    }

    public function __set($key, $value)
    {
        $this->_attr[$key] = $value;
    }

    public function have($key)
    {
        return array_key_exists($key, $this->_attr);
    }

    public function isEmpty()
    {
        return empty($this->_attr);
    }

    public function remove($key)
    {
        if ($this->have($key)) {
            unset($this->_attr[$key]);
        }
    }

    public function toAry()
    {
        return $this->_attr;
    }
}