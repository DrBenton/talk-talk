<?php

use TalkTalk\Core\Service\PackingProfilesManager;

$app->defineService(
    'packing-profiles-manager',
    function () use ($app) {
        $service = new PackingProfilesManager();
        $service->setPacksDir($app->vars['app.php_packs_path']);

        return $service;
    }
);
