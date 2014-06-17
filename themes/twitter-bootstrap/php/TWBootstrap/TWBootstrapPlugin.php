<?php

namespace TalkTalk\Theme\TWBootstrap;

use TalkTalk\Kernel\Plugin\PluginBase;

class TWBootstrapPlugin extends PluginBase
{

    public function registerHooks()
    {
        parent::registerHooks();

        // JS and CSS files & data
        $this->app->get('hooks')->onHook(
            'layout.js.get_data',
            array($this, 'onHookLayoutGetJsData')
        );

        // JS files to compile for production
        $this->app->get('hooks')->onHook(
            'layout.js.get_files_to_compile',
            array($this, 'onHookLayoutGetJsFilesToCompile')
        );
    }

    /**
     * @private
     */
    public function onHookLayoutGetJsData()
    {
        $myBootModule = $this->amdModulesBaseUrl . '/twbootstrap-init';
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


    /**
     * @private
     */
    public function onHookLayoutGetJsFilesToCompile()
    {
        $myJsAmdModulesRootUrl = $this->path . '/assets/js/amd';

        $myJsAmdModulesIds = array(
            //$myJsAmdModulesRootUrl . '/twbootstrap-init'
        );

        return $myJsAmdModulesIds;
    }

}