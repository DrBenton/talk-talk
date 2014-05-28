<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\Forum;
use TalkTalk\Model\Post;
use TalkTalk\Model\Topic;

$action = function (Application $app, Request $request, Forum $forum) {

    //TODO: validation

    $newTopicData = $request->request->get('topic');

    $newTopic = new Topic($newTopicData);
    $newTopic->forum_id = $forum->id;
    $newTopic->provider = 'talk-talk';
    $newTopic->author_id = $app['user']->id;
    $newTopic->save();

    $newPost = new Post($newTopicData);
    $newPost->forum_id = $forum->id;
    $newPost->topic_id = $newTopic->id;
    $newPost->author_id = $app['user']->id;
    $newPost->provider = 'talk-talk';
    $newPost->save();

    $app['session.flash.add.translated'](
        'core-plugins.forum-base.new-topic.alerts.new-topic-successful',
        array(),
        'success'
    );

    // And now, we just have to display this new Topic!
    $targetUrl = $app['url_generator']->generate('forum-base/topic', array(
        'topic' => $newTopic->id,
    ));
    if ($app['isAjax']) {
        // JS response
        return $app['twig']->render(
            'utils/common/simple-redirect.ajax.twig',
            array('targetUrl' => $targetUrl)
        );
    } else {
        // Real HTTP redirection to the page
        return $app->redirect($targetUrl);
    }
};

return $action;
