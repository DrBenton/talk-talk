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

        foreach ($pluginsMetadata as $pluginMetadata) {
            $this->unpackPlugin($pluginMetadata['id']);
        }
    }

    /**
     * @param  string                       $pluginId
     * @return \TalkTalk\Core\Plugin\Plugin
     */
    protected function unpackPlugin($pluginId)
    {
        $this->app
            ->get('packing-manager')
            ->unpackData($this->packsDataNs, $this->app->vars['plugins.packs_prefix'] . $pluginId);
    }

    protected function getPackedPluginsMetadataFile()
    {

    }

}
