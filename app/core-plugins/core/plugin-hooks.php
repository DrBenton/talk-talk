<?php

$hooks['define_javascript_app_config'] = function () use ($app) {
    return array(
        'debug' => $app->vars['debug'],
        'base_url' => $app->vars['app.base_url'],
    );
};
