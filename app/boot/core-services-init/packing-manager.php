<?php

use TalkTalk\Core\Service\PackingManager;

$app->defineService(
    'packing-manager',
    function () use ($app) {
        $service = new PackingManager();
        $service->setPacksDir($app->vars['app.php_packs_path']);

        return $service;
    }
);