<?php

define('BASE_DIR', dirname(dirname(dirname(__FILE__))));
putenv('ENV_LUMEN_ENV='.$argv[1]);

require_once BASE_DIR.'/vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../../config/', 'env_'.getenv('ENV_LUMEN_ENV')))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

\App\Support\Loader::init();