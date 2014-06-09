<?php

$app->before(
    function () use ($app) {
        $phpBbDbSettings = $app->get('session')->get('phpbb.db-settings');
        if (null !== $phpBbDbSettings) {
            $app->exec('phpbb.db.init', $phpBbDbSettings);
        }
    }
);