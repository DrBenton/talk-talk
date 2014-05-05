<?php

namespace TalkTalk\CorePlugins\Core\PluginsManagerBehaviour;

use TalkTalk\Core\Plugins\Manager\Behaviour\BehaviourBase;

class AssetsManager extends BehaviourBase
{

    public function registerPluginsAssets()
    {
        $pluginsManager = $this->pluginsManager;
        $app = $pluginsManager->getApp();

        $pluginsAssetsCss = array();
        $pluginsAssetsJs = array();

        foreach ($this->pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['assets'])) {
                continue;
            }

            if (isset($plugin->data['assets']['stylesheets'])) {
                foreach ($plugin->data['assets']['stylesheets'] as $jsAssetPath) {
                    $pluginsAssetsCss[] = $pluginsManager->handlePluginRelatedString($plugin, $jsAssetPath);
                }
            }

            if (isset($plugin->data['assets']['javascripts'])) {
                foreach ($plugin->data['assets']['javascripts'] as $jsAssetPath) {
                    $pluginsAssetsJs[] = $pluginsManager->handlePluginRelatedString($plugin, $jsAssetPath);
                }
            }
        }

        $app['plugins.assets.css'] = $pluginsAssetsCss;
        $app['plugins.assets.js'] = $pluginsAssetsJs;
        $this->logger->addDebug(
            sprintf(
                '%d CSS and %d JS assets registered by %d plugins.',
                count($pluginsAssetsCss),
                count($pluginsAssetsJs),
                count($this->pluginsManager->getPlugins())
            )
        );
    }

}
