<?php

try {
    (new Dotenv\Dotenv(__DIR__.'/../config/', 'env_'.getenv('ENV_LUMEN_ENV')))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

class LumenAppFrameWork extends Laravel\Lumen\Application
{
    public function storagePath($path = null)
    {
        return env('STORAGE_PATH', $this->basePath().'/storage').($path ? '/'.$path : $path);
    }
}
$lumen = new LumenAppFrameWork(
    realpath(__DIR__.'/../')
);

$lumen->register(App\Providers\EventServiceProvider::class);
$lumen->singleton('redis', function () use ($lumen) {
    return $lumen->loadComponent('database', 'Illuminate\Redis\RedisServiceProvider', 'redis');
});

if (env('EXCEPTION')) {
    error_reporting(E_ALL | E_STRICT);
} else {
    error_reporting(0);
}