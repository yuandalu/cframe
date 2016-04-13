<?php

namespace Elephant\Container;

class Factory
{
    private $_objs = array();

    public static function getInstance()
    {
        static $factory = null;
        if (is_null($factory)) {
            $factory = new static();
        }
        return $factory;
    }
    
    public static function find($name, $param = null)
    {
        $factory = self::getInstance();
        return $factory->get($name, $param);
    } 

    private function get($name, $param = null)
    {
        if (!isset($this->_objs[$name])) {
            $this->set($name, $param);
        }
        return $this->_objs[$name];
    }

    private function set($name, $param = null)
    {
        $this->_objs[$name] = new $name($param);
    }

}