<?php

namespace TalkTalk\Core;

use TalkTalk\Core\Services\ServiceDefinition;

class ServicesManager
{

    protected $servicesNamesPattern = '~^[a-z][a-zA-Z0-9]+$~';

    /**
     * @var \TalkTalk\Core\Application
     */
    protected $app;

    public function setApplication(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string|object $serviceClass
     * @param null|callable $initClosure
     * @return \TalkTalk\Core\Services\ServiceDefinition
     * @throws \RuntimeException
     */
    public function registerServiceClass($serviceClass, /*callable*/ $initClosure = null)
    {
        if (!class_implements($serviceClass, 'TalkTalk\Core\Services\ServiceInterface')) {
            throw new \RuntimeException(sprintf('Service class "%s" must implement ServiceInterface!', $serviceClass));
        }

        $serviceName = $serviceClass::getServiceName();

        $this->checkServiceName($serviceName);

        $app = $this->app;
        $serviceDefinition = new ServiceDefinition();
        $serviceDefinition->setServiceName($serviceName);
        $serviceDefinition->setServiceActivationClosure(
            function () use ($app, $serviceClass, &$initClosure) {

                if (is_string($serviceClass)) {
                    $serviceInstance = new $serviceClass();
                } else {
                    $serviceInstance = &$serviceClass;
                }
                $serviceInstance->setApplication($app);

                if (null !== $initClosure) {
                    if (!is_callable($initClosure)) {
                        throw new \RuntimeException(sprintf('Service class "%s" init Closure is not callable!', $serviceClass));
                    }
                    call_user_func($initClosure, $serviceInstance);
                }

                $serviceInstance->onActivation($app);

                return $serviceInstance;
            }
        );

        $this->registerServiceDefinitionToApp($serviceDefinition);

        return $serviceDefinition;
    }

    protected function registerServiceDefinitionToApp(ServiceDefinition $serviceDefinition)
    {
        $this->app->container->singleton(
            $serviceDefinition->getServiceName(),
            $serviceDefinition->getServiceActivationClosure()
        );
    }

    protected function checkServiceName($serviceName)
    {
        if (!preg_match($this->servicesNamesPattern, $serviceName)) {
            throw new \RuntimeException(sprintf('Invalid Service name "%s"!', $serviceName));
        }
    }
}