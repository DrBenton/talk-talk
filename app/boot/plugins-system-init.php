<?php

use TalkTalk\Core\Plugin\Plugin;

call_user_func(
    function () use ($app) {

        // Plugins specific app vars
        $app->vars['plugins.packs_namespace'] = 'plugins';
        $app->vars['plugins.packs_prefix'] = 'plugin---';
        $app->vars['plugins.registered_plugins'] = array();
        $app->vars['plugins.disabled_plugins'] = array(
            'permanently' => array(),
            'forCurrentUrl' => array(),
        );

        // "plugins.finder" Service init
        if (!$app->hasService('plugins.finder')) {
            $app->includeInApp($app->vars['app.boot_services_path'] . '/plugins-finder.php');
        }

        // Go! Let's find all our Plugins!
        $pluginsFinder = $app->get('plugins.finder');
        $pluginsFinder->findPlugins($app->vars['app.app_path'] . '/core-plugins');
        $pluginsFinder->findPlugins($app->vars['app.root_path'] . '/plugins');

        // Plugins init
        if (!$app->hasService('plugins.initializer')) {
            $app->includeInApp($app->vars['app.boot_services_path'] . '/plugins-initializer.php');
        }
        $app->get('plugins.initializer')->initPlugins();
    }
);
