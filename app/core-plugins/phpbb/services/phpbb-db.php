<?php

$app['phpbb.db.connection.name'] = 'phpbb';

$app['phpbb.db.initialized'] = false;

$app['phpbb.db.init'] = $app->protect(
    function (array $connectionSettings) use ($app) {
        $app['db.connection.add']($connectionSettings, $app['phpbb.db.connection.name']);
        $app['phpbb.db.initialized'] = true;
    }
);
