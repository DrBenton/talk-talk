<?php

namespace TalkTalk\Core\Service;

use TalkTalk\Core\Plugin\PackingBehaviour\PluginPackerBehaviourInterface;
use TalkTalk\Core\Plugin\Plugin;

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
            function (Plugin $plugin) {
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
     * @param Plugin $plugin
     */
    protected function generatePluginCode(Plugin $plugin)
    {
        $pluginPackedPhpCode = $plugin->getPhpCodeToPack();
        $this->app
            ->get('packing-manager')
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
            Plugin::getBehaviours(),
            function (PluginPackerBehaviourInterface $pluginsPacker) use (&$pluginsPackersInitCode) {

                $packerInitCode = $pluginsPacker->getPackerInitCode();

                if (null !== $packerInitCode) {
                    $packerClass = get_class($pluginsPacker);
                    $pluginsPackersInitCode .= <<<PACKER_INIT_CODE

/**
 * begin Plugin Packer "$packerClass" init code
 */
$packerInitCode
/**
 * end Plugin Packer "$packerClass" init code
 */

PACKER_INIT_CODE;
                }
            }
        );

        $this->app
            ->get('packing-manager')
            ->packPhpCode($pluginsPackersInitCode, $this->packsDataNs, 'plugins-packers-init');
    }

    public function generatePluginsMetadata(array $unpackedPlugins)
    {
        $pluginsMetadata = array();

        array_walk(
            $unpackedPlugins,
            function (Plugin $plugin) use (&$pluginsMetadata) {
                $metadata = $plugin->getMetadataToPack();
                $pluginsMetadata[$plugin->id] = $metadata;
            }
        );

        $this->app
            ->get('packing-manager')
            ->packData($pluginsMetadata, $this->packsDataNs, 'plugins-metadata');
    }

}
