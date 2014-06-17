<?php

namespace TalkTalk\Theme\TWBootstrap;

use TalkTalk\Kernel\Plugin\PluginBase;

class TWBootstrapPlugin extends PluginBase
{


    public function registerHooks()
    {
        parent::registerHooks();

        $this->app->get('hooks')->onHook('layout.js.get_data', array($this, 'onHookLayoutGetJsData'));
    }

    /**
     * @private
     */
    public function onHookLayoutGetJsData()
    {
        $myBootModule = $this->amdModulesBaseUrl . '/twbootstrap-init.js';
        return array(
            'bootModules' => array(
                $myBootModule
            ),
            'requireJsConfig' => array(
                'config' => array(
                    $myBootModule => array(
                        'myAssetsBaseUrl' => $this->assetsBaseUrl,
                        'twBootstrapDistBaseUrl' => $this->assetsBaseUrl . '/bower_components/bootstrap/dist',
                    )
                )
            )
        );
    }

}