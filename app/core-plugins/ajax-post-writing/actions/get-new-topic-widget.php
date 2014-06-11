<?php

use TalkTalk\Model\Forum;
use TalkTalk\Model\Topic;

$action = function (Forum $forum) use ($app) {
    return $app->get('view')->render('ajax-post-writing::new-topic', array(
        'forum' => $forum,
        'topic' => new Topic(),
    ));
};

return $action;