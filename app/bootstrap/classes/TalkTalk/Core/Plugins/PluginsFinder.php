<?php

namespace TalkTalk\Core\Plugins;

use Symfony\Component\Yaml\Yaml;
use TalkTalk\Core\Plugins\Manager\PluginsManagerInterface;

class PluginsFinder
{
    /**
     * @var \TalkTalk\Core\Plugins\Manager\PluginsManagerInterface
     */
    protected $_pluginsManager;

    public function __construct(PluginsManagerInterface $pluginsManager)
    {
        $this->_pluginsManager = $pluginsManager;
    }

    /**
     * @param string $rootPath
     * @param string $pluginsInitFilePattern
     * @param callable|null $pluginConfigParseFunc
     */
    public function findPlugins(
        $rootPath,
        $pluginsInitFilePattern,
        /*callable*/ $pluginConfigParseFunc = null
    ) {
        if (null === $pluginConfigParseFunc) {
            $pluginConfigParseFunc = function ($filePath) {
                return Yaml::parse($filePath);
            };
        }

        $pluginsInitFiles = glob($rootPath . $pluginsInitFilePattern);
        foreach ($pluginsInitFiles as $pluginInitFilePath) {

            $pluginConfigData = call_user_func($pluginConfigParseFunc, $pluginInitFilePath);
            $pluginPath = realpath(dirname($pluginInitFilePath));

            if (
                isset($pluginConfigData['general']['disabled']) &&
                $pluginConfigData['general']['disabled']
            ) {
                continue;
            }

            $this->_pluginsManager->addPlugin(
                new PluginData($pluginPath, $pluginConfigData)
            );

        }

    }

}
