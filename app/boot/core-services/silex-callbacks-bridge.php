<?php

use TalkTalk\Core\Service\SilexCallbacksBridge;

$app->defineService(
    'silex.callbacks_bridge',
    function () use ($app) {
        $service = new SilexCallbacksBridge();

        return $service;
    }
);
