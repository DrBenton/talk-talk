<?php

use TalkTalk\CorePlugin\Hooks\Service\Hooks;

$app->defineService(
    'hooks',
    function () use ($app) {
        $service = new Hooks();

        return $service;
    }
);
