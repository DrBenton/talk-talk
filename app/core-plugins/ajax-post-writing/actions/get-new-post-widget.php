<?php

use TalkTalk\Model\Topic;
use TalkTalk\Model\Post;

$action = function (Topic $topic) use ($app) {

    $post = new Post(array(
        'title' => $app->get('translator')->trans(
                'core-plugins.forum-base.new-post.form.title-default-content',
                array('%topic-title%' => $topic->title)
            )
    ));

    return $app->get('view')->render('ajax-post-writing::new-post', array(
        'topic' => $topic,
        'post' => $post,
    ));
};

return $action;
