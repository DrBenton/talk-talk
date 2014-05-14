<?php

$action = function (\Silex\Application $app) {
    $forumsTree = $app['forum.forums-data.tree'];
    return $app['twig']->render('forum-base/forums-display.twig',
        array(
            'forumsTree' => $forumsTree,
        )
    );
};

return $action;
