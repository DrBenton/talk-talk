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

        // Plugins specific boot services

        // Do we have packed Plugins?
        $app->includeInApp($app->vars['app.boot_services_path'] . '/plugins-unpacker.php');
        $hasPackedPlugins = $app->get('plugins.unpacker')->hasPackedPlugins();

        if (
            !$hasPackedPlugins ||
            !empty($app->vars['config']['packing']['always_repack_plugins'])
        ) {

            // No packed Plugins found.
            // --> let's find & pack them!

            // "plugins.finder" & "plugins.packer" Services init
            if (!$app->hasService('plugins.finder')) {
                $app->includeInApp($app->vars['app.boot_services_path'] . '/plugins-finder.php');
            }
            if (!$app->hasService('plugins.packer')) {
                $app->includeInApp($app->vars['app.boot_services_path'] . '/plugins-packer.php');
            }

            // Go! Let's pack all our Plugins!
            $app->get('plugins.packer')->packAllPlugins();

        }

        // All right, at this point we have packed Plugins PHP code,
        // whether is had been generated before or just now.
        // --> Let's unpack our Plugins super powers!

        $app->get('plugins.unpacker')->unpackPlugins();

    }
);
