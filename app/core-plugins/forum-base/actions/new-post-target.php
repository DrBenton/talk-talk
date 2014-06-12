<?php

use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\Post;
use TalkTalk\Model\Topic;

$action = function (Request $request, Topic $topic) use ($app) {

    //TODO: validation

    $newPostData = $request->request->get('post');
    $newPost = new Post($newPostData);
    $newPost->forum_id = $topic->forum()->id;
    $newPost->topic_id = $topic->id;
    $newPost->author_id = $app->get('user')->getUser()->id;
    $newPost->provider = 'talk-talk';
    $newPost->save();

    $app->get('flash')->flashTranslated(
        'alerts.success.new-post',
        'core-plugins.forum-base.new-post.alerts.new-post-successful',
        array()
    );

    // And now, we just have to display this new Post!
    $targetUrl = $app->path('forum-base/topic', array(
        'topic' => $topic->id,
        'page' => 'last',
    ));
    $targetUrl .= '#post-' . $newPost->id;
    if ($app->vars['isAjax']) {
        // JS response
        return $app->get('view')->render(
            'utils::common/simple-redirect.ajax',
            array('targetUrl' => $targetUrl)
        );
    } else {
        // Real HTTP redirection to the page
        return $app->redirect($targetUrl);
    }
};

return $action;
