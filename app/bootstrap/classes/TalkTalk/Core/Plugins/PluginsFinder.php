<?php

namespace TalkTalk\Core\Plugins;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\Yaml\Yaml;
use TalkTalk\Core\Plugins\Manager\PluginsManagerInterface;
use TalkTalk\Core\Utils\ArrayUtils;

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
    protected $enabledPlugins = array();
    protected $disabledPlugins = array();

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
    )
    {
        $this->findAndParsePluginsConfigurations($rootPath, $pluginsInitFilePattern, $pluginConfigParseFunc);

        $this->addEnabledPluginsToPluginsManager();
    }

    /**
     * @return int
     */
    public function getNbPlugins()
    {
        return count($this->enabledPlugins) + count($this->enabledPlugins);
    }

    /**
     * @return int
     */
    public function getNbPluginsPermanentlyDisabled()
    {
        return count($this->disabledPlugins);
    }

    /**
     * @return int
     */
    public function getNbPluginsDisabledForCurrentUrl()
    {
        return count($this->enabledPlugins) - count($this->pluginsManager->getPlugins());
    }

    protected function findAndParsePluginsConfigurations(
        $rootPath,
        $pluginsInitFilePattern,
        /*callable*/
        $pluginConfigParseFunc
    )
    {
        $cacheKey = self::CACHE_KEY . '-' . md5($rootPath);

        if ($this->cache->contains($cacheKey)) {

            // We have this data in cache, yee-ah!
            $allPluginsData = $this->cache->fetch($cacheKey);
            $this->enabledPlugins = $allPluginsData['enabled'];
            $this->disabledPlugins = $allPluginsData['disabled'];

            return;

        }

        // Oh. We don't have this data in cache...
        // All right, let's "glob()" and parse YAML!

        if (null === $pluginConfigParseFunc) {
            $pluginConfigParseFunc = array($this, 'defaultPluginConfigParseFunc');
        }

        $pluginsInitFiles = glob($rootPath . $pluginsInitFilePattern);
        foreach ($pluginsInitFiles as $pluginInitFilePath) {

            $pluginConfigData = call_user_func($pluginConfigParseFunc, $pluginInitFilePath);
            $pluginPath = realpath(dirname($pluginInitFilePath));

            $isPluginDisabled = $this->isPluginPermanentlyDisabled($pluginConfigData);

            $pluginId = (string) $pluginConfigData['@general']['id'];
            $pluginData = array(
                'id' => $pluginId,
                'path' => $pluginPath,
                'config' => $pluginConfigData,
            );

            if ($isPluginDisabled) {
                $this->disabledPlugins[] = $pluginData;
            } else {
                $this->enabledPlugins[] = $pluginData;
            }
        }

        // Pfwhee, this was expensive. Let's cache all this stuff...
        $allPluginsData = array(
            'enabled' => &$this->enabledPlugins,
            'disabled' => &$this->disabledPlugins,
        );
        $this->cache->save($cacheKey, $allPluginsData, self::CACHE_LIFETIME);
    }

    protected function addEnabledPluginsToPluginsManager()
    {
        // We have eliminated permanently disabled Plugins, but some Plugins
        // have a "only enable me for some type of URL" policy.
        // We now have to handle this!
        $app = $this->pluginsManager->getApp();
        $requestPath = $app->request->getResourceUri();

        foreach ($this->enabledPlugins as $pluginData) {

            if (
            $this->isPluginDisabledForRequestPath($requestPath, $pluginData['config'])
            ) {
                // The "enabledOnlyForUrl" policy of this Plugin disables it for this Request
                continue;
            }

            // Okay, this Plugin is added to our PluginsManager!
            $this->pluginsManager->addPlugin(
                new Plugin($pluginData['id'], $pluginData['path'], $pluginData['config'])
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

    /**
     * @param  array $pluginConfigData
     * @return bool
     */
    protected function isPluginPermanentlyDisabled(array &$pluginConfigData)
    {
        if (
            isset($pluginConfigData['@general']['disabled']) &&
            !!$pluginConfigData['@general']['disabled']
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param  string $requestPath
     * @param  array  $pluginConfigData
     * @return bool
     */
    protected function isPluginDisabledForRequestPath($requestPath, array &$pluginConfigData)
    {
        if (
        isset($pluginConfigData['@general']['enabledOnlyForUrl'])
        ) {
            $pathsPatterns = ArrayUtils::getArray($pluginConfigData['@general']['enabledOnlyForUrl']);
            foreach ($pathsPatterns as $pathPattern) {
                if (preg_match('~' . $pathPattern . '~', $requestPath)) {
                    return false;
                }
            }

            return true;
        }

        if (
        isset($pluginConfigData['@general']['enabledOnlyForItsActionsUrlsPrefix'])
        ) {
            $pluginUrlPrefix = $pluginConfigData['@general']['actionsUrlsPrefix'];
            if (!preg_match('~^' . $pluginUrlPrefix . '~', $requestPath)) {
                return true;
            }
        }

        return false;
    }

}
