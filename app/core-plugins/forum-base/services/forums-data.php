<?php

use TalkTalk\Model\Forum;

$app['forum-base.pagination.topics.nb_per_page'] = 20;

$app['forum-base.html.breadcrumb.get_single_forum'] = $app->protect(
    function (Forum $forum) use ($app) {
        return array(
            'url' => $app['url_generator']->generate('forum-base/forum', array('forumId' => $forum->id)),
            'label' => 'core-plugins.forum-base.breadcrumb.forum',
            'labelParams' => array('%name%' => $forum->name),
        );
    }
);

$app['forum-base.html.breadcrumb.get_forum_part'] = $app->protect(
    function (Forum $forum) use ($app) {
        $res = array();

        $parents = $forum->getParents();
        foreach ($parents as $parent) {
            $res[] = $app['forum-base.html.breadcrumb.get_single_forum']($parent);
        }

        $res[] = $app['forum-base.html.breadcrumb.get_single_forum']($forum);

        return $res;
    }
);
