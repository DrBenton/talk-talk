<?php

$app['twig'] = $app->share(
    $app->extend(
        'twig',
        function ($twig, $app) {
            $function = new Twig_SimpleFunction(
                'get_plugins_javascripts', function () use ($app) {
                    return $app['plugins.assets.js'];
                }
            );
            $twig->addFunction($function);

            return $twig;
        }
    )
);
