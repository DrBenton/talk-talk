<?php

use TalkTalk\CorePlugin\Utils\Service\Perfs;

$app->defineService(
    'perfs',
    function () use ($app) {
        $service = new Perfs();

        return $service;
    }
);
