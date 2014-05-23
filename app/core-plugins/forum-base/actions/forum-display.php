<?php

use TalkTalk\Model\Forum;
use TalkTalk\Model\Topic;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$action = function (Application $app, Request $request, Forum $forum) {

    // Forum children retrieval
    $forumChildren = $forum->getChildren();

    // Topics retrieval (only those of the current page)
    $pageNum = $request->query->getInt('page', 1);
    $topics = $forum->topics();
    $topicsToDisplay = $topics->getQuery()
        ->orderBy('updated_at', 'DESC')
        ->forPage(
            $pageNum,
            $app['forum-base.pagination.topics.nb_per_page']
        )
        ->get()
        ->load('author') /* "author" eager loading */
        ->all();

    // Total number of topics retrieval
    $nbTopicsTotal = Topic::where('forum_id', '=', $forum->id)->count();

    // Pagination stuff
    $paginationData = array(
        'currentPageNum' => $pageNum,
        'nbPages' => ceil($nbTopicsTotal / $app['forum-base.pagination.topics.nb_per_page']),
        'baseUrl' => $app['url_generator']->generate(
                'forum-base/forum', array('forum' => $forum->id)
            ) . '?page=%page%'
    );

    // Breadcrumb management
    $breadcrumb = array($app['utils.html.breadcrumb.home']);
    $breadcrumb = array_merge($breadcrumb, $app['forum-base.html.breadcrumb.get_forum_part']($forum));

    return $app['twig']->render('forum-base/forum-display.twig',
        array(
            'forum' => $forum,
            'forumChildren' => $forumChildren,
            'topics' => $topicsToDisplay,
            'nbTopicsTotal' => $nbTopicsTotal,
            'paginationData' => $paginationData,
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;
