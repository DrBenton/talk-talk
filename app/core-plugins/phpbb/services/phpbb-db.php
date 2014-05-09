<?php

$app['phpbb.db.connection.name'] = 'phpbb';

$app['phpbb.db.init'] = $app->protect(
    function (array $connectionSettings) use ($app){
        $app['db.connection.add']($connectionSettings, $app['phpbb.db.connection.name']);
    }
);
