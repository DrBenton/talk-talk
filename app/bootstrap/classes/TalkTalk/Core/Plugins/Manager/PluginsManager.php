<?php

namespace TalkTalk\Core\Plugins\Manager;

use Doctrine\Common\Cache\Cache;
use Psr\Log\LoggerInterface;
use Silex\Application;
use Symfony\Component\Security\Core\Exception\DisabledException;
use TalkTalk\Core\Plugins\Manager\Behaviour\BehaviourInterface;
use TalkTalk\Core\Plugins\Plugin;

class PluginsManager implements PluginsManagerInterface
{
    /**
     * @var \Silex\Application
     */
    protected $app;
    protected $plugins = array();
    protected $behaviours = array();
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    public function setApplication(Application $app)
    {
        $this->app = $app;
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
     * @param  string $filePath
     * @return mixed
     */
    public function includeFileInIsolatedClosure($filePath)
    {
        $app = $this->getApp();

        // A small security check: we only allow files inside the app
        $filePath = realpath($filePath);
        if (0 !== strpos($filePath, $app['app.path'])) {
            throw new DisabledException(sprintf('File path "%s" is not inside app directory!', $filePath));
        }

        $__includedFilePath = $filePath;

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
                str_replace($app['app.path'], $app['app.base_url'], $plugin->path),
                str_replace($app['app.path'], $app['app.base_url'], $app['app.vendors.js.path']),
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

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }
}
