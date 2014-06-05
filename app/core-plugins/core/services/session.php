<?php

use TalkTalk\CorePlugin\Core\Service\Session;

$app->defineService(
    'session',
    function () use ($app) {
        $service = new Session();

        return $service;
    }
);
