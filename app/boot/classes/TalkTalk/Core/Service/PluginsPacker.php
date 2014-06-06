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

    public function packAllPlugins()
    {
        $this->app->get('logger')->info(__METHOD__ . ' - Plugins packing.');
        $this->app->vars['plugins.packing.nb_packed'] = 0;
        $startTime = microtime(true);

        // Plugins core Packing Behaviours are added to the Unpacked Plugins class
        $corePackingBehaviours = array(
            'GeneralPacker',
            'ActionsPacker',
            'ClassesPacker',
            'ServicesPacker',
            'NewPackersPacker',
            'EventsPacker',
            'TranslationsPacker',
        );
        foreach ($corePackingBehaviours as $packerClassName) {
            $packerFullClassName = '\TalkTalk\Core\Plugin\PackingBehaviour\\' . $packerClassName;
            Plugin::addBehaviour(new $packerFullClassName);
        }

        // Core plugins discovery
        $corePluginsDir = $this->app->vars['app.app_path'] . '/core-plugins';
        $coreUnpackedPlugins = $this->app->get('plugins.finder')->findPlugins($corePluginsDir);

        // Third-party plugins discovery
        $thirdPartyPluginsDir = $this->app->vars['app.root_path'] . '/plugins';
        $thirdPartyUnpackedPlugins = $this->app->get('plugins.finder')->findPlugins($thirdPartyPluginsDir);

        // No third-party plugin can take the id of a core plugin
        $getPluginId = function (Plugin $plugin) {
            return strtolower($plugin->id);
        };
        $coreUnpackedPluginsIds = array_map($getPluginId, $coreUnpackedPlugins);
        $thirdPartyUnpackedPluginsIds = array_map($getPluginId, $thirdPartyUnpackedPlugins);
        $collisions = array_intersect($coreUnpackedPluginsIds, $thirdPartyUnpackedPluginsIds);
        if (count($collisions) > 0) {
            throw new \RuntimeException(sprintf(
                'The following Plugins ids are reserved, and cannot be chosen for third-party plugins: %s',
                implode(',', $coreUnpackedPluginsIds)
            ));
        }

        // Plugins packing
        $this->app->get('logger')->debug(
            sprintf('%s - Found %d core Plugins & %d third-party Plugins to pack.', __METHOD__, count($coreUnpackedPlugins), count($thirdPartyUnpackedPlugins))
        );
        $unpackedPlugins = array_merge($coreUnpackedPlugins, $thirdPartyUnpackedPlugins);
        $this->packPlugins($unpackedPlugins);

        $this->app->vars['plugins.packing.nb_packed'] = count($unpackedPlugins);
        $this->app->get('logger')->info(__METHOD__ . ' - Plugins packed. '.round((microtime(true) - $startTime) * 1000).'ms.');
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
        $this->app->get('logger')->debug(__METHOD__ . '()');
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
        $this->app->get('logger')->debug(sprintf('%s(%s) - plugin path=', __METHOD__, $plugin->id, $plugin->path));

        $pluginPackedPhpCode = $plugin->getPhpCodeToPack();

        $phpPackId = $this->app->vars['plugins.packs_prefix'] . $plugin->id;
        $this->app
            ->get('packing-manager')
            ->packPhpCode(
                $pluginPackedPhpCode,
                $this->packsDataNs,
                $phpPackId
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
        $this->app->get('logger')->debug(__METHOD__ . '()');

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
