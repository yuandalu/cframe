<?php

namespace App\Support;

class Entity extends SimpleObject
{
    public function __construct($attr = array())
    {
        parent::__construct($attr);
    }

    public function hashKey()
    {
        return '';
    }
}