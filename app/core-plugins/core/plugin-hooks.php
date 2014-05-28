<?php

$hooks['define_javascript_app_config'] = function () use ($app) {
    return array(
        'debug' => $app['debug'],
        'base_url' => $app['app.base_url'],
    );
};
