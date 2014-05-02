<?php

$app['twig'] = $app->share(
    $app->extend(
        'twig',
        function ($twig, $app) {
            $function = new Twig_SimpleFunction(
                'enable_html_hook', function ($hookName) use ($app) {
                    $app['plugins.html_hooks.add']($hookName);
                }
            );
            $twig->addFunction($function);

            return $twig;
        }
    )
);
