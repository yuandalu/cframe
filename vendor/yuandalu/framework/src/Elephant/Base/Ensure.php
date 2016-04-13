<?php

namespace Elephant\Base;

class Ensure
{
    static public function ensureNull($result, $msg)
    {
        if(!is_null($result))
            throw new RunException($msg);
    }

    static public function ensureNotNull($result, $msg)
    {
        if(is_null($result))
            throw new RunException($msg);
    }

    static public function ensureNotFalse($result, $msg)
    {
        if(false === $result)
            throw new RunException($msg);
    }

    static public function ensureFalse($result, $msg)
    {
        if(false !== $result)
            throw new RunException($msg);
    }

    static public function ensureEmpty($result, $msg)
    {
        if(true !== empty($result))
           throw new RunException($msg); 
    }

    static public function ensureNotEmpty($result, $msg)
    {
        if(true === empty($result))
            throw new RunException($msg);
    }
}