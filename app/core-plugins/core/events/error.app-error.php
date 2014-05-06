<?php

use Silex\Application;

$app->error(
    function (\Exception $e, $code) use ($app) {

        // We just inform the others app components that we have an error
        $app['app.error'] = $e;

        return;

    },
    Application::EARLY_EVENT
);
