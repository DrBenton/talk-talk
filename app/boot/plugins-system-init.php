<?php

use TalkTalk\Core\Plugin\UnpackedPlugin;
use TalkTalk\Core\Plugin\Config\CoreConfigHandler;

call_user_func(
    function () use ($app) {

        // Plugins specific app vars
        $app->vars['plugins.packs_namespace'] = 'plugins';

        // Plugins specific boot services

        // Do we have packed Plugins?
        // --> 1) We load the "plugins.unpacker" Service
        $app->includeInApp($app->vars['app.boot_services_path'] . '/plugins-unpacker.php');
        // --> 2) We use the "plugins.unpacker" Service
        $hasPackedPlugins = $app->getService('plugins.unpacker')->hasPackedPlugins();

        if (!$hasPackedPlugins) {

            // No packed Plugins found.
            // --> let's find & pack them!

            // "plugins.finder" & "plugins.packer" Services init
            $app->includeInApp($app->vars['app.boot_services_path'] . '/plugins-finder.php');
            $app->includeInApp($app->vars['app.boot_services_path'] . '/plugins-packer.php');

            // Core Plugins config handler is added to the Unpacked Plugins class
            UnpackedPlugin::addBehaviour(new CoreConfigHandler());

            // Core plugins discovery
            $corePluginsDir = $app->vars['app.app_path'] . '/core-plugins';
            $coreUnpackedPlugins = $app->getService('plugins.finder')->findPlugins($corePluginsDir);

            // Third-party plugins discovery
            $thirdPartyPluginsDir = $app->vars['app.root_path'] . '/plugins';
            $thirdPartyUnpackedPlugins = $app->getService('plugins.finder')->findPlugins($thirdPartyPluginsDir);

            // Plugins packing
            $unpackedPlugins = $coreUnpackedPlugins + $thirdPartyUnpackedPlugins;
            $app->getService('plugins.packer')->packPlugins($unpackedPlugins);

        }



    }
);