<?php

namespace TalkTalk\Core\Service;

use TalkTalk\Core\Plugin\UnpackedPlugin;
use Symfony\Component\Yaml\Yaml;

class PluginsFinder extends BaseService
{

    const PLUGINS_BEHAVIOURS_INIT_DIR = 'plugins-behaviours-init';
    const PLUGINS_BEHAVIOURS_INIT_FILE_PATTERN = '~^[a-z-]-behaviour\.php$~';

    protected  $pluginsConfigFilesPatten;

    public function setPluginsConfigFilesGlobPattern($pattern)
    {
        $this->pluginsConfigFilesPatten = $pattern;
    }

    /**
     * @param string $basePath
     * @return array an array of UnpackedPlugins
     */
    public function findPlugins($basePath)
    {
        $pluginsConfigFiles = glob($basePath . $this->pluginsConfigFilesPatten);

        return array_map(
            function ($pluginConfigFilePath) {
                $plugin = new UnpackedPlugin();
                $plugin->path = dirname($pluginConfigFilePath);
                $plugin->config = Yaml::parse($pluginConfigFilePath);

                return $plugin;
            },
            $pluginsConfigFiles
        );
    }

}