<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// HTML hooks actions will be triggered just before the Response sending
$app->after(
    function (Request $request, Response $response) use ($app) {
        $app['plugins.html_hooks.trigger_hooks']($response);
    }
);
