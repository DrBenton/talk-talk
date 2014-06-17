<?php

namespace TalkTalk\Kernel\Service;

use TalkTalk\Kernel\Plugin\PluginInterface;

class PluginsInitializer extends BaseService
{


    public function initPlugins(array $plugins)
    {
        foreach ($plugins as $plugin) {
            $this->initPlugin($plugin);
        }
    }

    protected function initPlugin(PluginInterface $plugin)
    {
        $plugin->setAssetsBaseUrl(
            $this->app->get('utils.string')->appPathToUrl($plugin->getPath() . '/assets')
        );
        $plugin->setVendorsBaseUrl(
            $this->app->get('utils.string')->appPathToUrl($this->app->vars['app.js_vendors_path'])
        );
        $plugin->registerHooks();
        $plugin->registerRestResources();
    }


}