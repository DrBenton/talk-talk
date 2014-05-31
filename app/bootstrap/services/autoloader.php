<?php

$app->container->singleton(
    'autoloader',
    function () use ($app) {
        return include $app->vars['app.path'] . '/vendor/php/autoload.php';
    }
);