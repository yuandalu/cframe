<?php

use Elephant\Container\Factory;
use App\Support\ObjectFinder;
use App\Support\Loader;
use App\Support\Logs;

if (!function_exists('render')) {
    function render($name, $noController = false)
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