<?php

namespace TalkTalk\Core;

use Slim\Slim;

class Application implements ApplicationInterface
{

    /**
     * @var array
     */
    public $vars = array();
    /**
     * @var \Slim\Slim
     */
    protected $slimApp;

    protected $definedServices = array();
    protected $resolvedServices = array();

    public function __construct(Slim $slimApp)
    {
        $this->slimApp = $slimApp;
        $this->vars['packs.included_files.closures'] = array();
        $this->registerAutoloader();
    }

    public function includeInApp($filePath)
    {
        $app = &$this;

        $filePath = $this->appPath($filePath);

        if (isset($this->vars['packs.included_files.closures'][$filePath])) {
            return call_user_func($this->vars['packs.included_files.closures'][$filePath], $app);
        }

        // Let's append the ".php" file extension if not already present
        $filePath .= (preg_match('~\.php$~i', $filePath)) ? '' : '.php' ;

        $fullFilePath = $this->vars['app.root_path'] . '/' . $filePath;

        if (!file_exists($fullFilePath)) {
            throw new \DomainException(sprintf('File to include "%s" not found!', $filePath));
        }

        // A small security check: we only allow files inside the app directory
        $this->checkAppPath($fullFilePath);

        $__includedFilePath = $fullFilePath;

        return call_user_func(
            function () use (&$app, $__includedFilePath) {
                return include $__includedFilePath;
            }
        );
    }

    public function appPath($absoluteFilePath)
    {
        return str_replace(
            array($this->vars['app.root_path'] . '/', '..', '//'),
            array('', '', '/'),
            $absoluteFilePath
        );
    }

    /**
     * @param  string            $pathToCheck
     * @throws \RuntimeException
     */
    public function checkAppPath($pathToCheck)
    {
        $realPath = realpath($pathToCheck);
        if (0 !== strpos($realPath, $this->vars['app.root_path'])) {
            throw new \DomainException(sprintf('Path "%s" is not inside app directory!', $pathToCheck));
        }
    }

    public function defineService($serviceId, $serviceDefinition)
    {
        $this->definedServices[$serviceId] = $serviceDefinition;
    }

    public function getService($serviceId)
    {
        if (isset($this->resolvedServices[$serviceId])) {
            return $this->resolvedServices[$serviceId];
        }

        if (!isset($this->definedServices[$serviceId])) {
            $errMsg = sprintf('No Service "%s" found!', $serviceId);
            if ($this->vars['debug']) {
                $errMsg .= sprintf('Available Services: %s', implode(', ', $this->definedServices));
            }
            throw new \DomainException($errMsg);
        }

        $serviceDefinition = $this->definedServices[$serviceId];

        if (is_callable($serviceDefinition)) {
            $serviceResolution = call_user_func($serviceDefinition);
        } else {
            $serviceResolution = $serviceDefinition;
        }

        if (is_object($serviceResolution) && $serviceResolution instanceof ApplicationAwareInterface) {
            $serviceResolution->setApplication($this);
        }

        if (null === $serviceResolution) {
            throw new \DomainException(sprintf('Service "%s" returned a null value!', $serviceId));
        }

        $this->definedServices[$serviceId] = $serviceResolution;

        return $serviceResolution;
    }

    public function hasService($serviceId)
    {
        return isset($this->definedServices[$serviceId]);
    }

    public function addAction($urlPattern)
    {
        return call_user_func_array(array($this->slimApp, 'map'), func_get_args());
    }

    public function run()
    {
        $this->slimApp->run();
    }

    public function before($callable, $priority = 0)
    {
        $this->slimApp->hook('slim.before', $callable, $priority);
    }

    public function after($callable, $priority = 0)
    {
        $this->slimApp->hook('slim.after', $callable, $priority);
    }

    protected function registerAutoloader()
    {
        spl_autoload_register(array($this, 'onClassAutoloadingRequest'));
    }

    protected function onClassAutoloadingRequest($className)
    {
        /*
        echo "onClassAutoloadingRequest($className) :: ";
        echo "\$this->hasService('packing-profiles-manager')=".$this->hasService('packing-profiles-manager').' :: ';
        echo "\$this->hasService('autoloader')=".$this->hasService('autoloader').' :: ';
        */
        if ($this->hasService('packing-profiles-manager')) {

            $packingProfilesManager = $this->getService('packing-profiles-manager');
            $hasPackedProfileForThisClass = $packingProfilesManager->hasPackedProfileForClass($className);

            if ($hasPackedProfileForThisClass) {
                // It seems that we have a packed PHP code that provides this class
                // --> let's unpack it!
                $packingProfilesManager->unpackProfileForClass($className);
            }

            if (class_exists($className, false) || interface_exists($className, false)) {
                $this->getService('logger')->debug(
                    sprintf('Class "%s" has been loaded with a PHP Pack.', $className)
                );
                // Mission complete!
                return;
            }

        }

        // No PHP pack provides this class. Let's give this job to Composer!
        if ($this->hasService('autoloader')) {
            $this->getService('logger')->debug(
                sprintf('Composer called to rescue to load class "%s".', $className)
            );
            $this->getService('autoloader')->loadClass($className);
        }
    }

}
