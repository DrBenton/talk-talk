<?php

$app['twig'] = $app->share(
    $app->extend(
        'twig',
        function ($twig, $app) {
            $function = new Twig_SimpleFunction(
                'display_app_debug_info', function () use ($app) {

                    $debugInfo = array();

                    // Plugins-related info
                    $pluginsFinder = $app['plugins.finder'];
                    $debugInfo['nbPlugins'] = $pluginsFinder->getNbPlugins();
                    $debugInfo['nbPluginsPermanentlyDisabled'] = $pluginsFinder->getNbPluginsPermanentlyDisabled();
                    $debugInfo['nbPluginsDisabledForCurrentUrl'] = $pluginsFinder->getNbPluginsDisabledForCurrentUrl();
                    // Silex-related info
                    $debugInfo['nbControllersRegistered'] = $app['routes']->count();

                    return $app['twig']->render(
                        'utils/debug/app-debug-info.twig',
                        $debugInfo
                    );
                },
                array('is_safe' => array('all'))
            );
            $twig->addFunction($function);

            return $twig;
        }
    )
);
