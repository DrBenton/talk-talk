<?php

use TalkTalk\Model\Forum;

$action = function (\Silex\Application $app, $forumId) {
    $forum = Forum::findOrFail($forumId);
    $breadcrumb = array($app['utils.html.breadcrumb.home']);
    $breadcrumb = array_merge($breadcrumb, $app['forum-base.html.breadcrumb.get_forum_part']($forum));

    return $app['twig']->render('forum-base/forum-display.twig',
        array(
            'forum' => $forum,
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;
