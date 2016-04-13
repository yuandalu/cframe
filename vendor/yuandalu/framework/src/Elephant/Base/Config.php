<?php

namespace Elephant\Base;

class Config
{
    public static $configFile = 'config/server_conf.php';

    public static function getConfig($property)
    {
        static $configs = array();
        if (array_key_exists($property,$configs)) {
            return $configs[$property];
        } else {
            $configs = self::getConfigVars();
            $config = isset($configs[$property]) ? $configs[$property] : '';
            return $config;
        }
        return false;
    }

    public static function getConfigVars()
    {
        include (self::$configFile);
        return get_defined_vars();
    }
}