<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$action = function (Application $app, Request $request) {

    $defaultDbSettings = array(
        'host' => 'localhost',
        'driver' => 'mysql',
        'username' => '',
        'prefix' => 'phpbb_',
        'database' => '',
        'port' => 3306,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    );

    return $app['twig']->render(
        'data-import-phpbb/start/start.twig',
        array('dbSettings' => $defaultDbSettings)
    );
};

return $action;
