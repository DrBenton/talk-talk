<?php

use TalkTalk\Model\Forum;

$app->defineFunction(
    'forum-base.get_site_title',
    function () use ($app) {
        static $res;
        if (!isset($res)) {
            $res = $app->get('settings')->get('app.site-title', 'Talk-Talk');
        }

        return $res;
    }
);

$app->vars['forum-base.pagination.topics.nb_per_page'] = 20;
$app->vars['forum-base.pagination.posts.nb_per_page'] = 20;

$app->defineFunction(
    'forum-base.html.breadcrumb.get_single_forum',
    function (Forum $forum) use ($app) {
        return array(
            'url' => $app->path('forum-base/forum', array('forum' => $forum->id)),
            'label' => 'core-plugins.forum-base.breadcrumb.forum',
            'labelParams' => array('%title%' => $forum->title),
        );
    }
);

$app->defineFunction(
    'forum-base.html.breadcrumb.get_forum_part',
    function (Forum $forum) use ($app) {
        $res = array();

        $parents = $forum->getParents();
        foreach ($parents as $parent) {
            $res[] = $app->exec('forum-base.html.breadcrumb.get_single_forum', $parent);
        }

        $res[] = $app->exec('forum-base.html.breadcrumb.get_single_forum', $forum);

        return $res;
    }
);