<?php

namespace TalkTalk\Core\Service;

use TalkTalk\Core\Plugin\Plugin;
use Symfony\Component\Yaml\Yaml;

class PluginsFinder extends BaseService
{

    protected $pluginsFilesPatten;
    protected $plugins = array();

    public function setPluginsFilesGlobPattern($pattern)
    {
        $this->pluginsFilesPatten = $pattern;
    }

    /**
     * @return array an array of Plugins
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
        $pluginsFiles = glob($basePath . $this->pluginsFilesPatten);

        $app = &$this->app;

        $this->plugins = array_merge($this->plugins, array_map(
            function ($pluginConfigFilePath) use ($app) {

                $plugin = new Plugin();
                $plugin->setApplication($app);
                $plugin->entryPoint = $pluginConfigFilePath;
                $plugin->path = $app->appPath(dirname($pluginConfigFilePath));
                $plugin->id = preg_replace('~^.+/([^/]+)/[^/]+$~', '$1', $pluginConfigFilePath);

                return $plugin;
            },
            $pluginsFiles
        ));
    }

}
