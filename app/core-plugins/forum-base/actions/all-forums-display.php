<?php

use TalkTalk\Model\Forum;

$action = function (\Silex\Application $app) {
    $forumsTree = Forum::getTree();
    $breadcrumb = array($app['utils.html.breadcrumb.home']);

    return $app['twig']->render('forum-base/all-forums-display.twig',
        array(
            'forumsTree' => $forumsTree,
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;
