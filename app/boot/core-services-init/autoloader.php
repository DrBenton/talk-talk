<?php

$app->defineService(
    'autoloader',
    function () use ($app) {
        return include $app->vars['app.php_vendors_path'] . '/autoload.php';
    }
);
