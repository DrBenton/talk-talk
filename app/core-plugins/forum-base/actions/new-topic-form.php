<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\Topic;
use TalkTalk\Model\Forum;

$action = function (Application $app, Request $request, Forum $forum) {

    // Breadcrumb management
    $breadcrumb = array($app['utils.html.breadcrumb.home']);
    $breadcrumb = array_merge($breadcrumb, $app['forum-base.html.breadcrumb.get_forum_part']($forum));
    $breadcrumb[] = array(
        'url' => $app['url_generator']->generate('forum-base/new-topic-form', array('forum' => $forum->id)),
        'label' => 'core-plugins.forum-base.breadcrumb.new_topic'
    );

    return $app['twig']->render(
        'forum-base/new-topic-form.twig',
        array(
            'forum' => $forum,
            'topic' => $request->get('topic', new Topic()),
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;
