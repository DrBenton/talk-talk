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

        $app = &$this->app;
        return array_map(
            function ($pluginConfigFilePath) use ($app) {

                $plugin = new UnpackedPlugin();
                $plugin->path = $app->appPath(dirname($pluginConfigFilePath));
                $plugin->config = Yaml::parse($pluginConfigFilePath);

                if (!isset($plugin->config['@general']['id'])) {
                    throw new \DomainException(sprintf('Plugin "%s" config file must define a "@general/id" value!', $pluginConfigFilePath));
                }

                $plugin->id = strtolower($plugin->config['@general']['id']);

                return $plugin;
            },
            $pluginsConfigFiles
        );
    }

}