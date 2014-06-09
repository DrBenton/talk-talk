<?php

$action = function () use ($app) {

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

    return $app->get('view')->render(
        'phpbb::import/start',
        array('dbSettings' => $defaultDbSettings)
    );
};

return $action;