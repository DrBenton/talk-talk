<?php

$app['twig'] = $app->share(
    $app->extend(
        'twig',
        function ($twig, $app) {
            $function = new Twig_SimpleFunction(
                'current_path',
                function () use ($app) {

                    return $app['url_generator']->generate(
                        $app['request']->attributes->get('_route'),
                        $app['request']->attributes->get('_route_params')
                    );
                }
            );
            $twig->addFunction($function);

            return $twig;
        }
    )
);
