<?php

use TalkTalk\Model\Forum;

$action = function (\Silex\Application $app, $forumId) {
    $forum = Forum::findOrFail($forumId);
    $breadcrumb = array(
        $app['utils.html.breadcrumb.home'],
        array(
            'url' => $app['url_generator']->generate('forum-base/forum', array('forumId' => $forum->id)),
            'label' => 'core-plugins.forum-base.forum-display.breadcrumb.1',
            'labelParams' => array('%name%' => $forum->name),
        ),
    );

    return $app['twig']->render('forum-base/forum-display.twig',
        array(
            'forum' => $forum,
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;
