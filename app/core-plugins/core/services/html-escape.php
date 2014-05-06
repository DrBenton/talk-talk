<?php

$app['html.escape'] = $app->protect(
    function ($string) use ($app) {
        $twigEnv = $app['twig']; //forces Twig initialization

        return twig_escape_filter($twigEnv, $string);
    }
);
