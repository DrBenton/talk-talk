<?php

use TalkTalk\Model\Forum;

$app['forum-base.title'] = $app->share(
    function () use ($app) {
        return $app['settings']->get('app.site-title', 'Talk-Talk');
    }
);

$app['forum-base.pagination.topics.nb_per_page'] = 20;
$app['forum-base.pagination.posts.nb_per_page'] = 20;

$app['forum-base.html.breadcrumb.get_single_forum'] = $app->protect(
    function (Forum $forum) use ($app) {
        return array(
            'url' => $app['url_generator']->generate('forum-base/forum', array('forum' => $forum->id)),
            'label' => 'core-plugins.forum-base.breadcrumb.forum',
            'labelParams' => array('%title%' => $forum->title),
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
