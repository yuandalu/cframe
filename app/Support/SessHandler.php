<?php

namespace App\Support;

class SessHandler implements \SessionHandlerInterface
{
    private $savePath;

    public function open($savePath , $sessionName)
    {
        return SessMysqlDriver::sessOpen();
    }

    public function close()
    {
        return SessMysqlDriver::sessClose();
    }

    public function read($sessionId)
    {
        return SessMysqlDriver::sessRead($sessionId);
    }

    public function write($sessionId , $sessionData)
    {
        return SessMysqlDriver::sessWrite($sessionId, $sessionData);
    }

    public function destroy($sessionId)
    {
        return SessMysqlDriver::sessDestroy($sessionId);
    }

    public function gc($maxlifetime)
    {
        return SessMysqlDriver::sessGc();
    }
}