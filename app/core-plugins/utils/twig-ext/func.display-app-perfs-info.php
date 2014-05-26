<?php

$app['twig'] = $app->share(
    $app->extend(
        'twig',
        function ($twig, $app) {
            $function = new Twig_SimpleFunction(
                'display_app_perfs_info',
                function () use ($app) {
                    return $app['twig']->render(
                        'utils/debug/app-perfs-info.twig',
                        $app['perfs.debug-info']
                    );
                },
                array('is_safe' => array('all'))
            );
            $twig->addFunction($function);

            return $twig;
        }
    )
);
