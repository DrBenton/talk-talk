<?php

use TalkTalk\CorePlugin\Core\Service\SessionFlash;

$app->defineService(
    'flash',
    function () use ($app) {
        $service = new SessionFlash();

        return $service;
    }
);
