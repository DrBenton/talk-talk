<?php

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

$app['phpbb.middleware.has-phpbb-settings-in-session'] = $app->protect(
    function () use ($app) {
        if (!$app['session']->has('phpbb.db-settings')) {
            throw new AccessDeniedException();
        }
    }
);