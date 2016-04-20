<?php

chdir(dirname(__FILE__));
error_reporting(E_ALL ^ E_NOTICE);
define('BASE_DIR', dirname(dirname(dirname(__FILE__))));
require BASE_DIR.'/bootstrap/autoload.php';
require_once '../../config/server_conf.php';
$ini = parse_ini_file('../../config/env_conf_'.$argv[1]);
foreach ($ini as $k => $v) {
    $_SERVER[$k]=$v;
}
\App\Support\Loader::init();