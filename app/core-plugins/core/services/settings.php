<?php

use TalkTalk\CorePlugin\Core\Service\Settings;

$app->defineService(
    'settings',
    function () use ($app) {
        $service = new Settings();

        return $service;
    }
);
