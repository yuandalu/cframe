<?php

namespace App\Support;

class ObjectFinder
{
    static $ins = null;
    private $_objs = array();

    public static function ins()
    {
        if (is_null(self::$ins)) {
            self::$ins = new ObjectFinder();
        }
        return self::$ins;
    }

    public static function have($name)
    {
        return self::ins()->haveImp($name);
    }

    private function haveImp($name)
    {
        return array_key_exists($name, $this->_objs);
    }

    public static function find($name)
    {
        return self::ins()->findImp($name);
    }

    private function findImp($name)
    {
        if ($this->haveImp($name)) {
            return $this->_objs[$name];
        }
        return null;
    }

    public static function registerByCls($cls)
    {
        return self::register(get_class($cls), $cls);
    }

    public static function register($name, $obj)
    {
        return self::ins()->registerImp($name, $obj);
    }

    private function registerImp($name, $obj)
    {
        if (!is_object($obj)) {
            return false;
        }
        if ($this->haveImp($name)) {
            return false;
        }
        $this->_objs[$name] = $obj;
        return true;
    }
    
    public static function destory($name)
    {
        return self::ins()->destoryImp($name);
    }
    
    private function destoryImp($name)
    {
        if ($this->haveImp($name)) {
            unset($this->_objs[$name]);
        }
    }
}