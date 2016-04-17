<?php

use Elephant\Container\Factory;
use App\Support\ObjectFinder;
use App\Support\Loader;
use App\Support\Logs;

if (!function_exists('view')) {
    function view($name, $noController = false)
    {
        $view = Factory::find('Elephant\Foundation\View');
        $view->setReturnAction($name);
        $view->setReturnNoController($noController);
        return $view;
    }
}

if (!function_exists('loader')) {
    function loader($name)
    {
        switch ($name) {
            case 'dbcache':
                return Loader::loadDBCache();
                break;
            case 'cache':
                return Loader::loadCache();
                break;
            case 'session':
                return Loader::loadSess();
                break;
            case 'httpsqs':
                return Loader::loadHttpsqs();
                break;
            case 'mredis':
                return Loader::loadMasterRedis();
                break;
            case 'sredis':
                return Loader::loadSlaveRedis();
                break;
            default:
                return null;
                break;
        }
        return null;
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