<?php

namespace App\Support;

class RedisDriver extends \Redis
{
    public function __construct($host, $port)
    {
        $this->connect($host, intval($port));
    }
}