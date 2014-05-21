<?php

use Carbon\Carbon;

$app['twig'] = $app->share(
    $app->extend(
        'twig',
        function ($twig, $app) {
            $filter = new Twig_SimpleFilter(
                'from_now',
                function ($date) use ($app) {
                    return with(new Carbon($date))->diffForHumans();
                }
            );
            $twig->addFilter($filter);

            return $twig;
        }
    )
);
