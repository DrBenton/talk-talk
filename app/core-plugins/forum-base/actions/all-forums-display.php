<?php

$action = function (\Silex\Application $app) {
    $forumsTree = $app['forum.forums-data.tree'];
    $breadcrumb = array($app['utils.html.breadcrumb.home']);

    return $app['twig']->render('forum-base/all-forums-display.twig',
        array(
            'forumsTree' => $forumsTree,
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;
