<?php

namespace TalkTalk\CorePlugins\Core\PluginsManagerBehaviour;

use TalkTalk\Core\Plugins\Manager\Behaviour\BehaviourBase;
use TalkTalk\Core\Utils\ArrayUtils;

class AssetsManager extends BehaviourBase
{

    public function registerPluginsAssets()
    {
        $app = $this->app;
        $pluginsManager = $this->pluginsManager;

        $pluginsAssetsCss = array();
        $pluginsAssetsJs = array('head' => array(), 'endOfBody' => array());

        foreach ($this->pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['@assets'])) {
                continue;
            }

            $assets = & $plugin->data['@assets'];
            if (isset($assets['stylesheets'])) {
                foreach ($assets['stylesheets'] as $cssAssetPath) {
                    $pluginsAssetsCss[] = $pluginsManager->handlePluginRelatedString($plugin, $cssAssetPath);
                }
            }

            if (isset($assets['javascripts'])) {
                foreach ($assets['javascripts'] as $jsAsset) {
                    $jsAsset = ArrayUtils::getArray($jsAsset, 'url');
                    $jsAsset['url'] = $pluginsManager->handlePluginRelatedString($plugin, $jsAsset['url']);
                    $target = (isset($jsAsset['head']) && true === $jsAsset['head']) ? 'head' : 'endOfBody';
                    $pluginsAssetsJs[$target][] = $jsAsset;
                }
            }
        }

        $app['plugins.assets.css'] = $pluginsAssetsCss;
        $app['plugins.assets.js.head'] = $pluginsAssetsJs['head'];
        $app['plugins.assets.js.endOfBody'] = $pluginsAssetsJs['endOfBody'];

        /*
        $this->logger->addDebug(
            sprintf(
                '%d CSS and %d JS assets registered by %d plugins.',
                count($pluginsAssetsCss),
                count($pluginsAssetsJs),
                count($this->pluginsManager->getPlugins())
            )
        );
        */
    }

}
