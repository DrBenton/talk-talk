<?php

$app['twig'] = $app->share(
    $app->extend(
        'twig',
        function ($twig, $app) {
            $function = new Twig_SimpleFunction(
                'get_flashes', function ($type = null) use ($app) {
                    if ($type === null) {
                        return $app['session.flash.get.all']();
                    } else {
                        return $app['session.flash.get']($type);
                    }
                }
            );
            $twig->addFunction($function);

            return $twig;
        }
    )
);
