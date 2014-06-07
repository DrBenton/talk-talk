<?php

namespace TalkTalk\Core\Service;

class SilexCallbacksBridge
{

    protected $registeredCallbacks = array();

    public function registerCallback($callbackId, $callable)
    {
        $this->registeredCallbacks[$callbackId] = $callable;
    }

    public function __call($name, $arguments)
    {
        if (!isset($this->registeredCallbacks[$name])) {
            throw new \DomainException(sprintf('No Silex bridged callback found for id "%s"!', $name));
        }

        $callback = call_user_func($this->registeredCallbacks[$name]);
        return call_user_func_array($callback, $arguments);
    }



}
