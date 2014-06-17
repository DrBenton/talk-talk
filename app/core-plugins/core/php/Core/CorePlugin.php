<?php

namespace TalkTalk\CorePlugin\Core;

use TalkTalk\Kernel\Plugin\PluginBase;
use TalkTalk\Kernel\ApplicationInterface;

class CorePlugin extends PluginBase
{

    public function registerRestResources()
    {
        parent::registerRestResources();

        $app = &$this->app;
        $app->addRestResource('GET', '/', function () use ($app) {
            return $app->get('template-renderer')->renderTemplate(__DIR__.'/Resources/templates/main-layout.tpl.php');
            //return $app->json(array('msg' => 'hello world!'));
        });
    }

    public function registerHooks()
    {
        parent::registerHooks();

        $this->app->get('hooks')->onHook(
            'layout.js.get_data',
            array($this, 'onHookLayoutGetJsData'),
            ApplicationInterface::EARLY_EVENT
        );
        $this->app->get('hooks')->onHook(
            'layout.js.get_scripts',
            array($this, 'onHookLayoutGetJsScripts'),
            ApplicationInterface::EARLY_EVENT
        );
    }

    /**
     * @private
     */
    public function onHookLayoutGetJsData()
    {
        return array(
          'debug' => $this->app->vars['debug'],
          'rootUrl' => $this->app->vars['app.root_url'],
          'vendorsRootUrl' => $this->vendorsBaseUrl,
          'bootModules' => array(),
        );
    }

    /**
     * @private
     */
    public function onHookLayoutGetJsScripts()
    {
        return array(
            $this->vendorsBaseUrl . '/requirejs/require.js',
            $this->javascriptsBaseUrl . '/requirejs-init.js',
            $this->javascriptsBaseUrl . '/main.js',
        );
    }


}