<?php

namespace TalkTalk\Core;

use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;

class Application implements ApplicationInterface
{

    /**
     * @var array
     */
    public $vars = array();
    /**
     * @var \Silex\Application
     */
    protected $silexApp;
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    protected $definedServices = array();
    protected $resolvedServices = array();
    protected $definedFunctions = array();
    protected $beforeRunCallbacks = array();

    public function __construct(SilexApplication $silexApp, Request $request)
    {
        $this->silexApp = $silexApp;
        $this->request = $request;
        $this->vars['packs.included_files.closures'] = array();
        $this->registerAutoloader();
        $this->defineService('silex', $this->silexApp);
    }

    public function setConfig(array $configData)
    {
        $this->vars['config'] = $configData;

        $this->vars['debug'] = (bool) $configData['debug']['debug'];
        $this->silexApp['debug'] = $this->vars['debug'];
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
     * @param  string           $pathToCheck
     * @throws \DomainException
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
                $errMsg .= ' ' . sprintf('Available Services: %s', implode(', ', array_keys($this->definedServices)));
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

    public function get($serviceId)
    {
        return $this->getService($serviceId);
    }

    public function hasService($serviceId)
    {
        return isset($this->definedServices[$serviceId]);
    }

    public function defineFunction($functionId, $callable)
    {
        $this->definedFunctions[$functionId] = $callable;
    }

    public function execFunction($functionId)
    {
        if (!isset($this->definedFunctions[$functionId])) {
            $errMsg = sprintf('No Function "%s" found!', $functionId);
            if ($this->vars['debug']) {
                $errMsg .= ' ' . sprintf('Available Functions: %s', implode(', ', array_keys($this->definedFunctions)));
            }
            throw new \DomainException($errMsg);
        }

        $args = array_slice(func_get_args(), 1);

        return call_user_func_array($this->definedFunctions[$functionId], $args);
    }

    public function exec($functionId)
    {
        return call_user_func_array(array($this, 'execFunction'), func_get_args());
    }

    public function getFunction($functionId)
    {
        if (!isset($this->definedFunctions[$functionId])) {
            $errMsg = sprintf('No Function "%s" found!', $functionId);
            if ($this->vars['debug']) {
                $errMsg .= sprintf('Available Functions: %s', implode(', ', array_keys($this->definedFunctions)));
            }
            throw new \DomainException($errMsg);
        }

        return $this->definedFunctions[$functionId];
    }

    public function addAction($urlPattern, $callback)
    {
        $this->get('logger')
            ->debug(sprintf('Action with URL "%s" added to Silex router.', $urlPattern));

        return call_user_func_array(array($this->silexApp, 'match'), func_get_args());
    }

    public function run()
    {
        $this->triggerBeforeRunCallbacks();

        $this->get('logger')
            ->debug('silexApp#run()');
        $this->silexApp->run($this->request);
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function beforeRun($callable, $priority = 0)
    {
        $this->beforeRunCallbacks[] = array(
            'callable' => $callable,
            'priority' => $priority,
        );
    }

    public function before($callable, $priority = 0)
    {
        $this->silexApp->before($callable, $priority);
    }

    public function after($callable, $priority = 0)
    {
        $this->silexApp->after($callable, $priority);
    }

    public function error($callable, $priority = 0)
    {
        $this->silexApp->error($callable, $priority);
    }

    /**
     * @param  string $actionName
     * @param  array  $params
     * @return string
     */
    public function path($actionName, $params = array())
    {
        static $urlGenerator;
        if (null === $urlGenerator) {
            if (!isset($this->silexApp['url_generator'])) {
                $this->silexApp->register(
                  new UrlGeneratorServiceProvider()
                );
            }
            $urlGenerator = $this->silexApp['url_generator'];
        }

        return $urlGenerator->generate($actionName, $params);
    }

    public function redirect($url, $status = 302)
    {
        $this->get('flash')->keepFlashes();

        return $this->silexApp->redirect($url, $status);
    }

    public function redirectToAction($actionName, $params = array(), $status = 302)
    {
        return $this->redirect(
            $this->path($actionName, $params),
            $status
        );
    }

    public function json($data = array(), $status = 200, array $headers = array())
    {
        return $this->silexApp->json($data, $status, $headers);
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
        if (
            !empty($this->vars['config']['packing']['use_vendors_packing']) &&
            $this->hasService('packing-profiles-manager')
        ) {

            $packingProfilesManager = $this->get('packing-profiles-manager');
            $hasPackedProfileForThisClass = $packingProfilesManager->hasPackedProfileForClass($className);

            if ($hasPackedProfileForThisClass) {
                // It seems that we have a packed PHP code that provides this class
                // --> let's unpack it!
                $packingProfilesManager->unpackProfileForClass($className);
            }

            if (class_exists($className, false) || interface_exists($className, false)) {
                $this->get('logger')->debug(
                    sprintf('Class "%s" has been loaded with a PHP Pack.', $className)
                );
                // Mission complete!
                return;
            }

        }

        // No PHP pack provides this class. Let's give this job to Composer!
        if ($this->hasService('autoloader')) {
            $this->get('logger')->info(
                sprintf('Composer called to rescue to load class "%s".', $className)
            );
            $this->get('autoloader')->loadClass($className);
        }
    }

    protected function triggerBeforeRunCallbacks()
    {
        $this->get('logger')
            ->debug(sprintf('triggerBeforeRunCallbacks() - %d callbacks registred', count($this->beforeRunCallbacks)));

        $this->get('utils.array')->sortBy($this->beforeRunCallbacks, 'priority');

        foreach ($this->beforeRunCallbacks as $beforeRunCallbackData) {
            call_user_func($beforeRunCallbackData['callable']);
        }
    }

}
