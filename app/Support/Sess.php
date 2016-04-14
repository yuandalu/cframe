<?php

namespace App\Support;

class Sess
{
    private $driver   = null;
    private $is_start = false;

    public function __construct($name = '', $driver = null)
    {
        $this->driver = $driver;
        $this->driver->init();
        session_name($name);
    }

    public function set($k, $v)
    {
        $this->ensureStart();
        $_SESSION[$k] = $v;
        return true;
    }

    public function get($k)
    {
        $this->ensureStart();
        if (array_key_exists($k, $_SESSION)) {
            return $_SESSION[$k];
        }
        return '';
    }

    public function getAll()
    {
        $this->ensureStart();
        return $_SESSION;
    }

    public function destroy($k)
    {
        $this->ensureStart();
        unset($_SESSION[$k]);
        return true;
    }

    public function destroyAll()
    {
        $this->ensureStart();
        $_SESSION = array();
        return true;
    }

    public function setSid($sid)
    {
        session_id($sid);
    }

    public function getSid()
    {
        $this->ensureStart();
        return session_id();
    }

    private function ensureStart()
    {
        if ($this->is_start) {
            return;
        }
        session_start();
        $this->is_start = true;
    }
}