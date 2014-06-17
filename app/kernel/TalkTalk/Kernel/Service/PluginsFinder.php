<?php

namespace TalkTalk\Kernel\Service;

use TalkTalk\Kernel\Plugin\PluginInterface;

class PluginsFinder extends BaseService
{

    protected $pluginsFilesPatten;
    protected $themesFilesPatten;
    protected $plugins = array();

    public function setPluginsFilesGlobPattern($pattern)
    {
        $this->pluginsFilesPatten = $pattern;
    }

    public function setThemesFilesGlobPattern($pattern)
    {
        $this->themesFilesPatten = $pattern;
    }

    /**
     * @return array an array of PluginInterfaces
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @param  string $basePath
     */
    public function findPlugins($basePath)
    {
        $pluginsFiles = glob($basePath . '/' . $this->pluginsFilesPatten);

        $this->plugins = array_merge($this->plugins, array_map(
            array($this, 'includePlugin'),
            $pluginsFiles
        ));
    }

    /**
     * @param  string $basePath
     */
    public function findThemes($basePath)
    {
        $themesFiles = glob($basePath . '/' . $this->themesFilesPatten);

        $this->plugins = array_merge($this->plugins, array_map(
            array($this, 'includePlugin'),
            $themesFiles
        ));
    }

    /**
     * @private
     */
    public function includePlugin($pluginFilePath)
    {
        $plugin = $this->app->includeInApp($pluginFilePath);
        if (!is_object($plugin) || !($plugin instanceof PluginInterface)) {
            throw new \DomainException(sprintf('Plugin file "%s" does not return a PluginInterface!', $pluginFilePath));
        }
        $plugin->setPath($this->app->appPath(dirname($pluginFilePath)));
        $plugin->setApplication($this->app);

        return $plugin;
    }

}