<?php

namespace TalkTalk\Core\Service;

use TalkTalk\Core\Plugin\PackingBehaviour\PluginPackerBehaviourInterface;
use TalkTalk\Core\Plugin\UnpackedPlugin;

class PluginsPacker extends BaseService
{

    protected $packsDataNs;

    public function setPacksDataNamespace($namespace)
    {
        $this->packsDataNs = $namespace;
    }

    /**
     * @param array $unpackedPlugins
     */
    public function packPlugins(array $unpackedPlugins)
    {
        $this->beforePacking($unpackedPlugins);

        $this->generatePluginsPackersInitCode();
        $this->generatePluginsCode($unpackedPlugins);
        $this->generatePluginsMetadata($unpackedPlugins);
    }

    protected function beforePacking(array $unpackedPlugins)
    {
        array_walk(
            $unpackedPlugins,
            function (UnpackedPlugin $plugin) {
                $plugin->beforePacking();
            }
        );
    }

    protected function generatePluginsCode(array $unpackedPlugins)
    {
        array_walk(
            $unpackedPlugins,
            array($this, 'generatePluginCode')
        );
    }

    /**
     * @param UnpackedPlugin $plugin
     */
    protected function generatePluginCode(UnpackedPlugin $plugin)
    {
        $pluginPackedPhpCode = $plugin->getPhpCodeToPack();
        $this->app
            ->getService('packing-manager')
            ->packPhpCode(
                $pluginPackedPhpCode,
                $this->packsDataNs,
                $this->app->vars['plugins.packs_prefix'] . $plugin->id
            );
    }

    public function generatePluginsPackersInitCode()
    {
        $pluginsPackersInitCode = '';

        array_walk(
            UnpackedPlugin::getBehaviours(),
            function (PluginPackerBehaviourInterface $pluginsPacker) use (&$pluginsPackersInitCode) {
                $pluginInitCode = $pluginsPacker->getPackerInitCode();
                if (null !== $pluginInitCode) {
                    $pluginsPackersInitCode .= $pluginInitCode;
                }
            }
        );

        $this->app
            ->getService('packing-manager')
            ->packPhpCode($pluginsPackersInitCode, $this->packsDataNs, 'plugins-packers-init');
    }

    public function generatePluginsMetadata(array $unpackedPlugins)
    {
        $pluginsMetadata = array();

        array_walk(
            $unpackedPlugins,
            function (UnpackedPlugin $plugin) use (&$pluginsMetadata) {
                $metadata = $plugin->getMetadataToPack();
                $pluginsMetadata[$plugin->id] = $metadata;
            }
        );

        $this->app
            ->getService('packing-manager')
            ->packData($pluginsMetadata, $this->packsDataNs, 'plugins-metadata');
    }

}
