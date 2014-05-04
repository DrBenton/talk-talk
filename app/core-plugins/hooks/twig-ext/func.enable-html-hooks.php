<?php

$app['twig'] = $app->share(
    $app->extend(
        'twig',
        function ($twig, $app) {
            $function = new Twig_SimpleFunction(
                'enable_html_hooks', function () use ($app) {
                    $hooksNames = func_get_args();
                    call_user_func_array($app['plugins.html_hooks.add'], $hooksNames);
                    if ($app['debug']) {
                        return '<!-- HTML hooks : '.implode(', ', $hooksNames).' -->';
                    }
                },
                array('is_safe' => array('all'))
            );
            $twig->addFunction($function);

            return $twig;
        }
    )
);
