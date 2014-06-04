<?php

use TalkTalk\Core\Service\ArrayUtils;

$app->defineService(
    'utils.array',
    function () use ($app) {
        $service = new ArrayUtils();

        return $service;
    }
);
