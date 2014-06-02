<?php

namespace TalkTalk\Core;

use Slim\Slim;
use TalkTalk\Core\ApplicationAwareInterface;

class Application
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
                return include_once $__includedFilePath;
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
     * @param string $pathToCheck
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
            throw new \RuntimeException(sprintf('No service "%s" found!', $serviceId));
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

        $this->definedServices[$serviceId] = $serviceResolution;

        return $serviceResolution;
    }

    public function addAction($urlPattern)
    {
        return call_user_func_array(array($this->slimApp, 'map'), func_get_args());
    }

    public function registerAutoloader()
    {
        spl_autoload_register(array($this, 'loadComposer'), true);
    }

    public function run()
    {
        $this->slimApp->run();
    }

    protected function loadComposer($class)
    {
        spl_autoload_unregister(array($this, 'loadComposer'));
        $this->getService('autoloader')->loadClass($class);
    }

}