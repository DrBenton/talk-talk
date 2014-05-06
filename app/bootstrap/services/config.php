<?php

$app['config'] = $app->share(function ($app) {
    $mainConfigFilePath = $app['app.path'] . '/app/config/main.ini.php';
    return parse_ini_file($mainConfigFilePath, true);
});
