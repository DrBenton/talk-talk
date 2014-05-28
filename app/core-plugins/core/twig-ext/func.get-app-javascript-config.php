<?php

$app['twig'] = $app->share(
    $app->extend(
        'twig',
        function ($twig, $app) {
            $function = new Twig_SimpleFunction(
                'get_app_javascript_config', function () use ($app) {
                    $jsConfigData = $app['plugins.trigger_hook']('define_javascript_app_config');
                    $jsConfigDataFlattened = call_user_func_array('array_merge', $jsConfigData);

                    return $jsConfigDataFlattened;
                }
            );
            $twig->addFunction($function);

            return $twig;
        }
    )
);
