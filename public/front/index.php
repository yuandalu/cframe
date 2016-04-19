<?php

define('BASE_DIR', dirname(dirname(dirname(__FILE__))));

require BASE_DIR.'/bootstrap/autoload.php';

\App\Support\Loader::init();
\App\Support\Loader::regSess('front');

$app = require_once BASE_DIR.'/bootstrap/app.php';

$app->setNameSpace('App\Controllers\Front');
$app->setControllerPath(BASE_DIR.'/app/Controllers/Front');
$app->setViewPath(BASE_DIR.'/resources/views/front/');

$app->run();

// $timer = new \App\Ext\Timer;
// $timer->s();
// echo '<br>';
// echo $timer->resourceUsage();