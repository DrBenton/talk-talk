<?php

use TalkTalk\Core\Plugin\UnpackedPlugin;

call_user_func(
    function () use ($app) {

        // Plugins specific app vars
        $app->vars['plugins.packs_namespace'] = 'plugins';
        $app->vars['plugins.packs_prefix'] = 'plugin---';
        $app->vars['plugins.registered_plugins'] = array();

        // Plugins specific boot services

        // Do we have packed Plugins?
        // --> 1) We load the "plugins.unpacker" Service
        $app->includeInApp($app->vars['app.boot_services_path'] . '/plugins-unpacker.php');
        // --> 2) We use the "plugins.unpacker" Service
        $hasPackedPlugins = $app->getService('plugins.unpacker')->hasPackedPlugins();

        if (
            !$hasPackedPlugins ||
            (
                isset($config['debug']['packing.always_repack_plugins']) &&
                true == $config['debug']['packing.always_repack_plugins']
            )
        ) {

            $app->getService('logger')->info('Plugins packing.');

            // No packed Plugins found.
            // --> let's find & pack them!

            // "plugins.finder" & "plugins.packer" Services init
            $app->includeInApp($app->vars['app.boot_services_path'] . '/plugins-finder.php');
            $app->includeInApp($app->vars['app.boot_services_path'] . '/plugins-packer.php');

            // Plugins core Packing Behaviours are added to the Unpacked Plugins class
            UnpackedPlugin::addBehaviour(new \TalkTalk\Core\Plugin\PackingBehaviour\GeneralPacker());
            UnpackedPlugin::addBehaviour(new \TalkTalk\Core\Plugin\PackingBehaviour\ActionsPacker());
            UnpackedPlugin::addBehaviour(new \TalkTalk\Core\Plugin\PackingBehaviour\ClassesPacker());
            UnpackedPlugin::addBehaviour(new \TalkTalk\Core\Plugin\PackingBehaviour\ServicesPacker());
            UnpackedPlugin::addBehaviour(new \TalkTalk\Core\Plugin\PackingBehaviour\NewPackersPacker());

            // Core plugins discovery
            $corePluginsDir = $app->vars['app.app_path'] . '/core-plugins';
            $coreUnpackedPlugins = $app->getService('plugins.finder')->findPlugins($corePluginsDir);

            // Third-party plugins discovery
            $thirdPartyPluginsDir = $app->vars['app.root_path'] . '/plugins';
            $thirdPartyUnpackedPlugins = $app->getService('plugins.finder')->findPlugins($thirdPartyPluginsDir);

            // No third-party plugin can take the id of a core plugin
            $getPluginId = function (UnpackedPlugin $plugin) {
                return strtolower($plugin->id);
            };
            $coreUnpackedPluginsIds = array_map($getPluginId, $coreUnpackedPlugins);
            $thirdPartyUnpackedPluginsIds = array_map($getPluginId, $thirdPartyUnpackedPlugins);
            $collisions = array_intersect($coreUnpackedPluginsIds, $thirdPartyUnpackedPluginsIds);
            if (count($collisions) > 0) {
                throw new \RuntimeException(sprintf(
                    'The following Plugins ids are reserved, and cannot be chosen for third-party plugins: %s',
                    implode(',', $coreUnpackedPluginsIds)
                ));
            }

            // Plugins packing
            $unpackedPlugins = $coreUnpackedPlugins + $thirdPartyUnpackedPlugins;
            $app->getService('plugins.packer')->packPlugins($unpackedPlugins);

        }

        // All right, at this point we have packed Plugins PHP code,
        // whether is had been generated before or just now.
        // --> Let's unpack our Plugins super powers!

        $app->getService('plugins.unpacker')->unpackPlugins();


    }
);