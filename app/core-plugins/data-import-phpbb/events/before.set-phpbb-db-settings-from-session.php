<?php

$app->before(
    function () use ($app) {
        $phpBbDbSettings = $app['session']->get('phpbb.db-settings');
        if (null !== $phpBbDbSettings) {
            $app['data-import-phpbb.db.connection.set_settings']($phpBbDbSettings);
        }
    }
);
