<?php

namespace TalkTalk\Core\Plugins\Manager;

use Silex\Application;
use TalkTalk\Core\Plugins\Manager\Behaviour\BehaviourInterface;
use TalkTalk\Core\Plugins\PluginData;

class PluginsManager implements PluginsManagerInterface
{
    /**
     * @var \Silex\Application
     */
    protected $_app;
    protected $_plugins = array();
    protected $_behaviours = array();

    public function setApplication(Application $app)
    {
        $this->_app = $app;
    }

    public function addPlugin(PluginData $plugin)
    {
        $this->_plugins[] = $plugin;
    }

    public function addBehaviour(BehaviourInterface $behaviour)
    {
        $behaviour->setPluginsManager($this);
        $this->_behaviours[] = $behaviour;
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
        $__includedFilePath = $filePath;

        return call_user_func(
            function () use (&$app, $__includedFilePath) {
                return include_once $__includedFilePath;
            }
        );
    }

    public function handlePluginRelatedString(PluginData $plugin, $pluginRelatedString)
    {
        $app = $this->getApp();

        return str_replace(
            array('${pluginPath}', '${pluginUrl}', '${vendorsUrl}'),
            array(
                $plugin->pluginPath,
                str_replace($app['app.path'], $app['app.base_url'], $plugin->pluginPath),
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
        return $this->_app;
    }

    /**
     * @return array
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }

    public function __call($name, array $arguments)
    {
        foreach ($this->_behaviours as $behaviour) {
            if (method_exists($behaviour, $name)) {
                return call_user_func_array(array($behaviour, $name), $arguments);
            }
        }

        throw new \RuntimeException(
            sprintf('No behaviour found for method "%s" (%u registered behaviours)', $name, count($this->_behaviours))
        );
    }
}
