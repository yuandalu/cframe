<?php

$app = Elephant\Container\Factory::find('Elephant\Foundation\Application', BASE_DIR); 
Elephant\Base\Config::$configFile = BASE_DIR.'/config/server_conf.php';
$app->throwException(Elephant\Base\Config::getConfig('EXCEPTION'));

return $app;