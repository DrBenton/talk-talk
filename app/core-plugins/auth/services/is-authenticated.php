<?php

$app['isAuthenticated'] = $app->share(
    function () use ($app) {
        return $app['session']->has('userId');
    }
);

$app['isAnonymous'] = $app->share(
    function () use ($app) {
        return !$app['isAuthenticated'];
    }
);
