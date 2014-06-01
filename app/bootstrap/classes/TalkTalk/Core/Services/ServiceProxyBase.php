<?php

namespace TalkTalk\Core\Services;

abstract class ServiceProxyBase extends ServiceBase
{
    /**
     * @var mixed
     */
    protected $proxyTarget;

    public function __construct($anInstanceToProxy = null)
    {
        if (null !== $anInstanceToProxy) {
            $this->setProxyTarget($anInstanceToProxy);
        }
    }

    public function setProxyTarget($anInstanceToProxy)
    {
        $this->proxyTarget = $anInstanceToProxy;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->proxyTarget, $method), $args);
    }

    public function __set($name, $value)
    {
        $this->proxyTarget->$name = $value;
    }

    public function __get($name)
    {
        return $this->proxyTarget->$name;
    }

}