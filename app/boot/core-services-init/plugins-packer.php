<?php

use TalkTalk\Core\Service\PluginsPacker;

$app->defineService(
    'plugins.packer',
    function () use ($app) {
        $service = new PluginsPacker();
        $service->setPacksDataNamespace($app->vars['plugins.packs_namespace']);

        return $service;
    }
);
