<?php

$phpBbConnectionSettings = null;

$app['data-import-phpbb.db.connection.name'] = 'phpbb';

$app['data-import-phpbb.db.connection.set_settings'] = $app->protect(
    function (array $connectionSettings) use (&$phpBbConnectionSettings) {

        $phpBbConnectionSettings = $connectionSettings;
    }
);

$app['data-import-phpbb.db.connection.factory'] = function () use ($app, &$phpBbConnectionSettings) {

    if (null === $phpBbConnectionSettings) {
        throw new \RuntimeException('phpBb connection requested without prior settings definition!');
    }

    return $app['db.connection.factory']($phpBbConnectionSettings, $app['data-import-phpbb.db.connection.name']);
};

$app['data-import-phpbb.db'] = $app->share(
    function () use ($app) {
        return $app['data-import-phpbb.db.connection.factory'];
    }
);
