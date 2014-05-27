<?php

use TalkTalk\Model\Forum;

$action = function (\Silex\Application $app) {
    $forumsTree = Forum::getTree();
    $breadcrumb = array($app['utils.html.breadcrumb.home']);

    $siteTitle = $app['forum-base.title'];

    return $app['twig']->render('forum-base/home.twig',
        array(
            'forumsTree' => $forumsTree,
            'siteTitle' => $siteTitle,
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;
