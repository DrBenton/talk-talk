<?php

namespace TalkTalk\Core\Services;

class ServiceDefinition
{
    /**
     * @var string
     */
    protected $serviceName;
    /**
     * @var \Closure
     */
    protected $serviceActivationClosure;

    public function setServiceName($name)
    {
        $this->serviceName = $name;
    }

    public function setServiceActivationClosure(/*callable*/ $initClosure)
    {
        if (!is_callable($initClosure)) {
            throw new \RuntimeException('Service activation closure is not callable!');
        }
        $this->serviceActivationClosure = $initClosure;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @return callable
     */
    public function getServiceActivationClosure()
    {
        return $this->serviceActivationClosure;
    }

}