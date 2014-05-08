<?php

$app->before(
    function () use ($app) {
        $phpBbDbSettings = $app['session']->get('phpbb.db-settings');
        if (null !== $phpBbDbSettings) {
            $app['phpbb.db.connection.set_settings']($phpBbDbSettings);
        }
    }
);
