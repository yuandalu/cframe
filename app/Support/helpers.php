<?php

use App\Support\ObjectFinder;
use App\Support\DBCache;
use App\Support\Logs;

if (!function_exists('loader')) {
    function loader($name)
    {
        $obj = ObjectFinder::find($name);
        if (is_object($obj)) {
            return $obj;
        }
        switch ($name) {
            case 'DBCache':
                $obj = new DBCache();
                ObjectFinder::register('DBCache', $obj);
                break;
            
            default:
                return null;
                break;
        }
        return $obj;
    }
}

if (!function_exists('env')) {
    function env($name, $location = 'fastcgi')
    {
        if ($location == 'fastcgi') {
            return $_SERVER[$name];
        } elseif ($location == 'local') {
            // 帮助函数载入比较优先，所以app.php设置的配置路径不生效，在此重新设置
            Elephant\Base\Config::$configFile = BASE_DIR.'/config/server_conf.php';
            return \Elephant\Base\Config::getConfig($name);
        }
    }
}

if (!function_exists('logs')) {
    function logs($name, $only = false)
    {
        $obj = ObjectFinder::find('Logs_'.$name);
        if (is_object($obj)) {
            return $obj;
        }

        $fname = env('ENV_APPLOGS_DIR').'/'.$name.'.log';
        $envShell = env('SHELL');
        if (isset($envShell)) {
            $fname.= '_shell';
        }
        if (!$only) {
            $fname.= '.'.date('Ymd');
        }
        $obj = new Logs($fname);
        ObjectFinder::register('Logs_'.$name, $obj);
        return $obj;
    }
}