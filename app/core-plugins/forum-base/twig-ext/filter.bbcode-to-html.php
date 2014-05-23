<?php

$app['twig'] = $app->share(
    $app->extend(
        'twig',
        function ($twig, $app) {
            $filter = new Twig_SimpleFilter(
                'bbcode_to_html',
                function ($bbcode) use ($app) {
                    return $app['forum-base.markup-manager.handle_forum_markup.all']($bbcode);
                }
            );
            $twig->addFilter($filter);

            return $twig;
        }
    )
);
