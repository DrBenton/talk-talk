<?php

use TalkTalk\CorePlugin\Core\Service\Csrf;

$app->defineService(
    'csrf',
    function () use ($app) {
        $service = new Csrf();

        return $service;
    }
);
