<?php

use TalkTalk\Core\Service\IOUtils;

$app->defineService(
    'utils.io',
    function () use ($app) {
        $service = new IOUtils();

        return $service;
    }
);