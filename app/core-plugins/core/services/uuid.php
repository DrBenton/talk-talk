<?php

use TalkTalk\CorePlugin\Core\Service\Uuid;

$app->defineService(
    'uuid',
    function () use ($app) {
        $service = new Uuid();

        return $service;
    }
);
