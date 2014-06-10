<?php

use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\Topic;
use TalkTalk\Model\Forum;

$action = function (Request $request, Forum $forum) use ($app) {

    // Breadcrumb management
    $breadcrumbData = array($app->exec('utils.html.breadcrumb.get_home_part'));
    $breadcrumbData = array_merge($breadcrumbData, $app->exec('forum-base.html.breadcrumb.get_forum_part', $forum));
    $breadcrumbData[] = array(
        'url' => $app->path('forum-base/new-topic-form', array('forum' => $forum->id)),
        'label' => 'core-plugins.forum-base.breadcrumb.new_topic'
    );

    return $app->get('view')->render(
        'forum-base::new-topic-form',
        array(
            'forum' => $forum,
            'topic' => $request->get('topic', new Topic()),
            'breadcrumbData' => $breadcrumbData,
        )
    );
};

return $action;