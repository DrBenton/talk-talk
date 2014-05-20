<?php

$app['twig'] = $app->share(
    $app->extend(
        'twig',
        function ($twig, $app) {
            $function = new Twig_SimpleFunction(
                'get_plugins_javascripts', function ($type = 'normal') use ($app) {
                    if (!in_array($type, array('endOfBody', 'head'))) {
                        throw new \RuntimeException(sprintf('Invalid JavaScript type "%s"!', $type));
                    }
                    return $app['plugins.assets.js.' . $type];
                }
            );
            $twig->addFunction($function);

            return $twig;
        }
    )
);
