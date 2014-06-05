<?php

namespace TalkTalk\CorePlugin\Core\Service;

class Session
{

    public function __construct()
    {
        session_start();
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key, $defaultValue=null)
    {
        return (isset($_SESSION[$key])) ? $_SESSION[$key] : $defaultValue ;
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function clear($key)
    {
        unset($_SESSION[$key]);
    }

    public function clearAll()
    {
        $_SESSION[] = array();
    }

}
