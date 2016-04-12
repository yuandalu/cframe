<?php
class QFrameConfig
{
    static public $configFile = 'server_conf.php';

    static public function getConfig($property)
    {/*{{{*/
        static $configs = array();
        if(array_key_exists($property,$configs))
        {
            return $configs[$property];
        }else
        {
            $configs = self::getConfigVars();
            $config = isset($configs[$property]) ? $configs[$property] : '';
            return $config;
        }
        return false;
    }/*}}}*/

    static public function getConfigVars()
    {/*{{{*/
        include (self::$configFile);
        return get_defined_vars();
    }/*}}}*/
}
?>
