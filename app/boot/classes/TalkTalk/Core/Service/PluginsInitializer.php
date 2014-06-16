<?php

namespace TalkTalk\Core\Service;

use TalkTalk\Core\Plugin\Plugin;
use TalkTalk\Core\Service\BaseService;

class PluginsInitializer extends BaseService
{

    public function initPlugins()
    {
        $plugins = $this->app->get('plugins.finder')->getPlugins();

        foreach ($plugins as $plugin) {
            $this->initPlugin($plugin);
        }
    }

    protected function initPlugin(Plugin $plugin)
    {
        static $vendorsUrl;
        if (null === $vendorsUrl) {
            $vendorsUrl = $this->app->vars['app.base_url'] . '/' . $this->app->appPath($this->app->vars['app.js_vendors_path']);
        }

        $app = &$this->app;
        $assets = array();
        call_user_func(
            function () use ($app, $plugin, &$assets) {
                include_once $plugin->entryPoint;
            }
        );

        foreach (array('css', 'js.head', 'js.endOfBody') as $assetType) {
            if (!empty($assets[$assetType])) {
                foreach ($assets[$assetType] as $assetData) {
                    $assetData['url'] = $this->app->get('utils.string')->handlePluginRelatedString($plugin, $assetData['url']);
                    $app->vars['app.assets.' . $assetType][] = $assetData;
                }
            }
        }
    }

}