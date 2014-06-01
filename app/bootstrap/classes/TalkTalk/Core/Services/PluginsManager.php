<?php

namespace TalkTalk\Core\Services;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Slim\Log;
use TalkTalk\Core\Plugins\Manager\PluginsManagerInterface;
use TalkTalk\Core\Plugins\Manager\Behaviour\BehaviourInterface;
use TalkTalk\Core\Plugins\Plugin;

class PluginsManager extends ServiceBase implements ServiceInterface, PluginsManagerInterface
{
    /**
     * @var array
     */
    protected $plugins = array();
    /**
     * @var array
     */
    protected $behaviours = array();
    /**
     * @var \Slim\Log
     */
    protected $logger;
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * @inheritdoc
     */
    public static function getServiceName()
    {
        return 'pluginsManager';
    }

    public function addPlugin(Plugin $plugin)
    {
        $this->plugins[$plugin->id] = $plugin;
    }

    public function getPlugin($pluginId)
    {
        return $this->plugins[$pluginId];
    }

    public function addBehaviour(BehaviourInterface $behaviour)
    {
        $behaviour->setPluginsManager($this);
        $behaviour->setLogger($this->logger);
        $behaviour->setCache($this->cache);
        $this->behaviours[] = $behaviour;
    }

    /**
     * Includes a file within an isolated Closure, and returns its result (if any).
     * The file PHP core will only have access to a "$app" variable.
     *
     * @param  string                                                       $filePath
     * @throws \Symfony\Component\Security\Core\Exception\DisabledException
     * @return mixed
     */
    public function includeFileInIsolatedClosure($filePath)
    {
        $app = $this->getApp();

        if (!file_exists($filePath)) {
            throw new \RuntimeException(sprintf('File path "%s" not found!', $filePath));
        }

        // A small security check: we only allow files inside the app
        $fileRealPath = realpath($filePath);
        if (0 !== strpos($fileRealPath, $app->vars['app.path'])) {
            throw new DisabledException(sprintf('File path "%s" is not inside app directory!', $filePath));
        }

        $__includedFilePath = $fileRealPath;

        return call_user_func(
            function () use (&$app, $__includedFilePath) {
                return include_once $__includedFilePath;
            }
        );
    }

    public function handlePluginRelatedString(Plugin $plugin, $pluginRelatedString)
    {
        $app = $this->getApp();

        return str_replace(
            array('%pluginPath%', '%pluginUrl%', '%vendorsUrl%'),
            array(
                $plugin->path,
                str_replace($app->vars['app.path'], $app->vars['app.base_url'], $plugin->path),
                str_replace($app->vars['app.path'], $app->vars['app.base_url'], $app->vars['app.vendors.js.path']),
            ),
            $pluginRelatedString
        );
    }

    /**
     * @return \Silex\Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    public function __call($name, array $arguments)
    {
        foreach ($this->behaviours as $behaviour) {
            if (is_callable(array($behaviour, $name))) {
                return call_user_func_array(array($behaviour, $name), $arguments);
            }
        }

        throw new \RuntimeException(
            sprintf('No behaviour found for method "%s" (%u registered behaviours)', $name, count($this->behaviours))
        );
    }

    public function setLogger(Log $logger)
    {
        $this->logger = $logger;
    }

    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

}
