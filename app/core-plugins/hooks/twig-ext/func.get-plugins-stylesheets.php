<?php

$app['twig'] = $app->share(
    $app->extend(
        'twig',
        function ($twig, $app) {
            $function = new Twig_SimpleFunction(
                'get_plugins_stylesheets', function () use ($app) {
                    return $app['plugins.assets.css'];
                }
            );
            $twig->addFunction($function);

            return $twig;
        }
    )
);
