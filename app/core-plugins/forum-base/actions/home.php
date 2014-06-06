<?php

use TalkTalk\Model\Forum;

$action = function () use ($app) {
    $forumsTree = Forum::getTree();
    $breadcrumbData = array($app->exec('utils.html.breadcrumb.get_home_part'));

    $siteTitle = $app->exec('forum-base.get_site_title');

    return $app->get('view')->render('forum-base::home',
        array(
            'forumsTree' => $forumsTree,
            'siteTitle' => $siteTitle,
            'breadcrumbData' => $breadcrumbData,
        )
    );
};

return $action;
