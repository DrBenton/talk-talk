<?php

$app->defineService(
    'autoloader',
    function () use ($app) {

        return $app->vars['app.php_vendors_path'] . '/autoload.php';
    }
);