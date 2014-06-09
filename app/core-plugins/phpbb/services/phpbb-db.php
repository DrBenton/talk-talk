<?php

$app->vars['phpbb.db.connection.name'] = 'phpbb';

$app->vars['phpbb.db.initialized'] = false;

$app->defineFunction(
    'phpbb.db.init',
    function (array $connectionSettings) use ($app) {
        $app->exec('db.connection.add', $connectionSettings, $app->vars['phpbb.db.connection.name']);
        $app->vars['phpbb.db.initialized'] = true;
    }
);