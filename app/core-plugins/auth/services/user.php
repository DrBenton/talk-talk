<?php

use TalkTalk\CorePlugin\Auth\Service\User;

$app->defineService(
    'user',
    function () use ($app) {
        $service = new User();

        return $service;
    }
);
