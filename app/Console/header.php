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

try {
    (new Dotenv\Dotenv(__DIR__.'/../../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

\App\Support\Loader::init();