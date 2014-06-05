<?php

use TalkTalk\Core\Service\Logger;

$app->vars['logger.target-dir'] = $app->vars['app.var_path'] . '/logs' ;

$app->defineService(
    'logger',
    function () use ($app) {
        $service = new Logger($app->vars['logger.target-dir']);

        return $service;
    }
);
