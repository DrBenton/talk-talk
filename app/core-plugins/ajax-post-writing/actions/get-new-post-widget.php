<?php

use TalkTalk\Model\Topic;
use TalkTalk\Model\Post;

$action = function (Topic $topic) use ($app) {
    return $app->get('view')->render('ajax-post-writing::new-post', array(
        'topic' => $topic,
        'post' => new Post(),
    ));
};

return $action;
