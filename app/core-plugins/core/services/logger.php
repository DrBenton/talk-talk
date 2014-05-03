<?php

$app->register(
    new Silex\Provider\MonologServiceProvider(),
    array(
        'monolog.logfile' => $app['app.var.logs.path'] . '/app.log',
    )
);
