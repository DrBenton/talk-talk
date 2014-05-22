<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->after(
    function (Request $request, Response $response) use ($app) {

        if (!isset($app['perfs.querypath.duration'])) {
            return;
        }
        
        $response->headers->add(array(
            'X-QueryPath-Duration' => $app['perfs.querypath.duration']
        ));
    },
    -1 // we want to run this *after* the QueryPath "after" hook
);