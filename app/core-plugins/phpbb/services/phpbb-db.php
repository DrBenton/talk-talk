<?php

$phpBbConnectionSettings = null;

$app['phpbb.db.connection.name'] = 'phpbb';

$app['phpbb.db.connection.set_settings'] = $app->protect(
    function (array $connectionSettings) use (&$phpBbConnectionSettings) {

        $phpBbConnectionSettings = $connectionSettings;
    }
);

$app['phpbb.db.connection.factory'] = function () use ($app, &$phpBbConnectionSettings) {

    if (null === $phpBbConnectionSettings) {
        throw new \RuntimeException('phpBb connection requested without prior settings definition!');
    }

    return $app['db.connection.factory']($phpBbConnectionSettings, $app['phpbb.db.connection.name']);
};

$app['phpbb.db'] = $app->share(
    function () use ($app) {
        return $app['phpbb.db.connection.factory'];
    }
);
