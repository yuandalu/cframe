<?php

define('BASE_DIR', dirname(dirname(dirname(__FILE__))));

if (in_array(getenv('REMOTE_ADDR'), array('127.0.0.1')) || substr(getenv('REMOTE_ADDR'), 0, 8) == '192.168.') {
    error_reporting(E_ALL | E_STRICT);
    putenv("EXCEPTION=true");
} else {
    error_reporting(0);
    putenv("EXCEPTION=false");
}

require_once BASE_DIR.'/vendor/autoload.php';
require_once BASE_DIR.'/bootstrap/lumen.php';

\App\Support\Loader::init();
\App\Support\Loader::regSess('admin');

$app = Elephant\Container\Factory::find('Elephant\Foundation\Application', BASE_DIR);
$app->throwException(env('EXCEPTION'));

$app->setNameSpace('App\Controllers\Admin');
$app->setControllerPath(BASE_DIR.'/app/Controllers/Admin');
$app->setViewPath(BASE_DIR.'/resources/views/admin/');

$app->run();

// $timer = new \App\Ext\Timer;
// $timer->s();
// echo '<br>';
// echo $timer->resourceUsage();

/*
 * 自定义路由规则
 *
  $userroute = new QFrameStandRoute(
       'u/:qid',
       array(
              'controller' => 'my',
              'action'     => 'index',
       )
);
Container::find('QFrameRouter')->addRoute('user',$userroute);
 */