<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\Post;
use TalkTalk\Model\Topic;

$action = function (Application $app, Request $request, Topic $topic)
{

    //TODO: validation

    $newPostData = $request->request->get('post');
    $newPost = new Post($newPostData);
    $newPost->forum_id = $topic->forum()->id;
    $newPost->topic_id = $topic->id;
    $newPost->author_id = $app['user']->id;
    $newPost->provider = 'talk-talk';
    $newPost->save();

    $app['session.flash.add.translated'](
        'core-plugins.forum-base.new-post.alerts.new-post-successful',
        array(),
        'success'
    );

    // And now, we just have to display this new Post!
    $targetUrl = $app['url_generator']->generate('forum-base/topic', array(
        'topic' => $topic->id,
        'page' => 'last',
    ));
    $targetUrl .= '#post-' . $newPost->id;
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