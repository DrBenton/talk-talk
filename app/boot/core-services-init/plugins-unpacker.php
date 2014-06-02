<?php

use TalkTalk\Core\Service\PluginsUnpacker;

$app->defineService(
    'plugins.unpacker',
    function () use ($app) {
        $service = new PluginsUnpacker();
        $service->setPacksDataNamespace($app->vars['plugins.packs_namespace']);

        return $service;
    }
);