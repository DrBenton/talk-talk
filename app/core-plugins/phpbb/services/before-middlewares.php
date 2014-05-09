<?php

$app['phpbb.middleware.require-phpbb-connection-settings'] = $app->protect(
    function () use ($app) {
        if (!$app['session']->has('phpbb.db-settings')) {
            throw new \RuntimeException('No phpBb settings defined!');
        }
    }
);
