<?php

use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\Forum;
use TalkTalk\Model\Topic;

$action = function (Request $request, Forum $forum) use ($app) {

    // Forum children retrieval
    $forumChildren = $forum->getChildren();

    // Topics retrieval (only those of the current page)
    $pageNum = $request->query->getInt('page', 1);
    $topics = $forum->topics();
    $topicsToDisplay = $topics->getQuery()
        ->orderBy('updated_at', 'DESC')
        ->forPage(
            $pageNum,
            $app->vars['forum-base.pagination.topics.nb_per_page']
        )
        ->get()
        ->load('author') /* "author" eager loading */
        ->all();

    // Total number of topics retrieval
    $nbTopicsTotal = Topic::where('forum_id', '=', $forum->id)->count();

    // Pagination stuff
    $paginationData = array(
        'currentPageNum' => $pageNum,
        'nbPages' => ceil($nbTopicsTotal / $app->vars['forum-base.pagination.topics.nb_per_page']),
        'baseUrl' => $app->path(
            'forum-base/forum', array('forum' => $forum->id)
        ) . '?page=%page%'
    );

    // Breadcrumb management
    $breadcrumbData = array($app->exec('utils.html.breadcrumb.get_home_part'));
    $breadcrumbData = array_merge($breadcrumbData, $app->exec('forum-base.html.breadcrumb.get_forum_part', $forum));

    return $app->get('view')->render('forum-base::forum-display',
        array(
            'forum' => $forum,
            'forumChildren' => $forumChildren,
            'topics' => $topicsToDisplay,
            'nbTopicsTotal' => $nbTopicsTotal,
            'paginationData' => $paginationData,
            'breadcrumbData' => $breadcrumbData,
        )
    );
};

return $action;
