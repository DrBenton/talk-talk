<?php

$app['isAuthenticated'] = $app->share(
    function () use ($app) {
        return $app['session']->has('user');
    }
);

$app['isAnonymous'] = $app->share(
    function () use ($app) {
        return !$app['isAuthenticated'];
    }
);
