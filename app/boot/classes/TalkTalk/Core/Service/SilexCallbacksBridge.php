<?php

namespace TalkTalk\Core\Service;

use TalkTalk\Core\ApplicationInterface;

class SilexCallbacksBridge extends BaseService
{

    protected $registeredCallbacks = array();

    public function setApplication(ApplicationInterface $app)
    {
        parent::setApplication($app);

        $silexApp = $this->app->get('silex');
        if (!isset($silexApp['talk_talk_callbacks'])) {
            $silexApp['talk_talk_callbacks'] = $this;
        }
    }

    public function registerCallback($callbackId, $callable)
    {
        $callbackId = $this->app->get('utils.string')->camelize($callbackId);
        $this->registeredCallbacks[$callbackId] = $callable;
    }

    public function __call($name, $arguments)
    {
        $name = $this->app->get('utils.string')->camelize($name);
        if (!isset($this->registeredCallbacks[$name])) {
            throw new \DomainException(sprintf('No Silex bridged callback found for id "%s"!', $name));
        }

        $callback = call_user_func($this->registeredCallbacks[$name]);

        return call_user_func_array($callback, $arguments);
    }

}
