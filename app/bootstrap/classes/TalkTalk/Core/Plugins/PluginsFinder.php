<?php

namespace TalkTalk\Core\Plugins;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\Yaml\Yaml;
use TalkTalk\Core\Plugins\Manager\PluginsManagerInterface;

class PluginsFinder
{

    const CACHE_KEY = 'talk-talk/plugins-finder/plugins-data';
    const CACHE_LIFETIME = 4000;

    /**
     * @var \TalkTalk\Core\Plugins\Manager\PluginsManagerInterface
     */
    protected $pluginsManager;
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    public function __construct(PluginsManagerInterface $pluginsManager)
    {
        $this->pluginsManager = $pluginsManager;
    }

    /**
     * @param string        $rootPath
     * @param string        $pluginsInitFilePattern
     * @param callable|null $pluginConfigParseFunc
     */
    public function findPlugins(
        $rootPath,
        $pluginsInitFilePattern,
        /*callable*/
        $pluginConfigParseFunc = null
    ) {
        if (null === $pluginConfigParseFunc) {
            $pluginConfigParseFunc = array($this, 'defaultPluginConfigParseFunc');
        }

        $cacheKey = self::CACHE_KEY . '-' . md5($rootPath);

        if ($this->cache->contains($cacheKey)) {

            // We have this data in cache, yee-ah!
            $pluginsData = $this->cache->fetch($cacheKey);

        } else {

            // Oh. We don't have this data in cache...
            // All right, let's "glob()" and parse YAML!
            $pluginsData = array();

            $pluginsInitFiles = glob($rootPath . $pluginsInitFilePattern);
            foreach ($pluginsInitFiles as $pluginInitFilePath) {

                $pluginConfigData = call_user_func($pluginConfigParseFunc, $pluginInitFilePath);
                $pluginPath = realpath(dirname($pluginInitFilePath));

                if (
                    isset($pluginConfigData['@general']['disabled']) &&
                    !!$pluginConfigData['@general']['disabled']
                ) {
                    continue;
                }

                $pluginId = (string) $pluginConfigData['@general']['id'];
                $pluginsData[] = array(
                    'id' => $pluginId,
                    'path' => $pluginPath,
                    'config' => $pluginConfigData,
                );
            }

            // Pfwhee, this was expensive. Let's cache all this stuff...
            $this->cache->save($cacheKey, $pluginsData, self::CACHE_LIFETIME);
        }

        foreach ($pluginsData as $pluginData) {
            $this->pluginsManager->addPlugin(
                new PluginData($pluginData['id'], $pluginData['path'], $pluginData['config'])
            );
        }

    }

    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    protected function defaultPluginConfigParseFunc($pluginFilePath)
    {
        return Yaml::parse($pluginFilePath);
    }

}
