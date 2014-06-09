<?php

namespace TalkTalk\Core\Service;

use TalkTalk\Core\Plugin\Plugin;

class PluginsUnpacker extends BaseService
{

    protected $packsDataNs;

    public function setPacksDataNamespace($namespace)
    {
        $this->packsDataNs = $namespace;
    }

    /**
     * @return bool
     */
    public function hasPackedPlugins()
    {
        return $this->app
            ->get('packing-manager')
            ->hasPackedData($this->packsDataNs, 'plugins-metadata');
    }

    public function unpackPlugins()
    {
        // Plugins Packers init code
        $this->app
            ->get('packing-manager')
            ->unpackData($this->packsDataNs, 'plugins-packers-init');

        // Plugins unpacking
        $pluginsMetadata = $this->app
            ->get('packing-manager')
            ->unpackData($this->packsDataNs, 'plugins-metadata');

        // Well... Each Plugin is now unpacked! (if its is enabled)
        foreach ($pluginsMetadata as $pluginMetadata) {

            if ($this->isPermanentlyDisabled($pluginMetadata)) {
                continue;
            }
            if ($this->isDisabledForCurrentUrl($pluginMetadata)) {
                continue;
            }

            $this->unpackPlugin($pluginMetadata['id'], $pluginMetadata);
        }
    }

    /**
     * @param  string $pluginId
     * @return \TalkTalk\Core\Plugin\Plugin
     */
    protected function unpackPlugin($pluginId)
    {
        $this->app
            ->get('packing-manager')
            ->unpackData($this->packsDataNs, $this->app->vars['plugins.packs_prefix'] . $pluginId);
    }

    protected function isPermanentlyDisabled(array $pluginMetadata)
    {
        static $requestPathInfo;

        if (empty($pluginMetadata['disabled'])) {
            return false;
        }

        $this->app->vars['plugins.disabled_plugins']['permanently'][] = $pluginMetadata['id'];

        $this->app->get('logger')
            ->debug(sprintf('Plugin "%s" is permanently disabled"', $pluginMetadata['id']));

        return false;
    }

    protected function isDisabledForCurrentUrl(array $pluginMetadata)
    {
        static $requestPathInfo;

        if (!isset($pluginMetadata['enabledOnlyForUrl'])) {
            return false;
        }

        if (null === $requestPathInfo) {
            $requestPathInfo = $this->app->getRequest()->getPathInfo();
        }

        foreach ($pluginMetadata['enabledOnlyForUrl'] as $whiteListUrlPattern) {
            if (preg_match('~'.$whiteListUrlPattern.'~', $requestPathInfo)) {
                return false;
            }
        }

        $this->app->vars['plugins.disabled_plugins']['forCurrentUrl'][] = $pluginMetadata['id'];

        $this->app->get('logger')
            ->debug(sprintf('Plugin "%s" is disabled for current URL "%s"', $pluginMetadata['id'], $requestPathInfo));

        return true;
    }
}
