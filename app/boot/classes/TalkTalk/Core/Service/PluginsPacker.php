<?php

namespace TalkTalk\Core\Service;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class PluginsPacker extends BaseService
{

    protected $packsDataNs;

    public function setPacksDataNamespace($namespace)
    {
        $this->packsDataNs = $namespace;
    }

    /**
     * @param string $unpackedPlugins
     * @return string the Plugins PHP pack metadata file path
     */
    public function packPlugins($unpackedPlugins)
    {
        $pluginsMetadata = array();
        foreach($unpackedPlugins as $unpackedPlugin) {

            $this->packPlugin($unpackedPlugin);

            $metadata = $unpackedPlugin->getMetadataToPack();
            $pluginsMetadata[$unpackedPlugin->id] = $metadata;

        }

        return $this->generatePluginsMetadata($pluginsMetadata);
    }

    /**
     * @param string $pluginPath
     * @return string the PHP pack file path
     */
    protected function packPlugin(UnpackedPlugin $plugin)
    {
        $pluginPackedPhpCode = $plugin->getPhpCodeToPack();
        $this->app
            ->getService('packing-manager')
            ->packPhpCode($pluginPackedPhpCode, $this->packsDataNs, $this->app->vars['plugins.packs_prefix'] . $plugin->id);
    }

    public function generatePluginsMetadata(array $pluginsMetadata)
    {
        $this->app
            ->getService('packing-manager')
            ->packData($pluginsMetadata, $this->packsDataNs, 'metadata');
    }

}