<?php

QFrameConfig::$configFile = '../config/server_conf.php';

$app = QFrame::createWebApp();
$app->throwException( QFrameConfig::getConfig('EXCEPTION') );

return $app;