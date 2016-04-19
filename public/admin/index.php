<?php

define('BASE_DIR', dirname(dirname(dirname(__FILE__))));

require BASE_DIR.'/bootstrap/autoload.php';

\App\Support\Loader::init();
\App\Support\Loader::regSess('admin');

$app = require_once BASE_DIR.'/bootstrap/app.php';

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