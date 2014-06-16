<?php

use TalkTalk\Core\Service\PluginsInitializer;

$app->defineService(
    'plugins.initializer',
    function () use ($app) {
        $service = new PluginsInitializer();

        return $service;
    }
);
