<?php

use TalkTalk\Core\Service\StringUtils;

$app->defineService(
    'utils.string',
    function () use ($app) {
        $service = new StringUtils();

        return $service;
    }
);