<?php

$app['phpbb.middleware.init-phpbb-connection-or-fail'] = $app->protect(
    function () use ($app) {

        $phpBbDbSettings = $app['session']->get('phpbb.db-settings');
        if (null === $phpBbDbSettings) {
            throw new \RuntimeException('No phpBb settings defined!');
        }

        // Because Illuminate always tries to initialize the "default" DB connection,
        // we always have to init our Talk-Talk DB... :-/
        $defaultDbConnection = $app['db'];

        // Let's initialize the PhpBb DB connection!
        $app['phpbb.db.connection.set_settings']($phpBbDbSettings);
        $phpBbDbConnection = $app['phpbb.db'];
    }
);
