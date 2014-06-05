<?php

use TalkTalk\CorePlugin\Core\Service\Crypto;

$app->defineService(
    'crypto',
    function () use ($app) {
        $service = new Crypto();

        return $service;
    }
);
