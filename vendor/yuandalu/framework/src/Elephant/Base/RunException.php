<?php

namespace Elephant\Base;

class RunException extends \RuntimeException 
{
    public function __construct($message, $code=0)
    {
        parent::__construct($message, $code);
    } 
}