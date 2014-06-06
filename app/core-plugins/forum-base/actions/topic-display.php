<?php

use TalkTalk\Model\Topic;
use TalkTalk\Model\Post;

$action = function ($topicId) use ($app) {

    $topic = Topic::findOrFail($topicId);
    $topic->load('author');

    // Total number of posts retrieval
    $nbPostsTotal = Post::where('topic_id', '=', $topic->id)->count();

    // Posts retrieval (only those of the current page)
    $pageNum = (int) $app->vars['request']->get('page', 1);
    if ('last' === $pageNum) {
        $pageNum = ceil($nbPostsTotal / $app->vars['forum-base.pagination.posts.nb_per_page']);
    } elseif (!is_numeric($pageNum)) {
        $pageNum = 1;
    }

    $posts = $topic->posts();
    $postsToDisplay = $posts->getQuery()
        ->orderBy('created_at', 'ASC')
        ->forPage(
            $pageNum,
            $app->vars['forum-base.pagination.posts.nb_per_page']
        )
        ->get()
        ->load('author') /* "author" eager loading */
        ->all();

    /**
     * Check eager loading with MySQL queries logs :-)
     * @see http://stackoverflow.com/questions/650238/how-to-show-the-last-queries-executed-on-mysql
     */

    // Pagination stuff
    $topicUrl = $app->path(
        'forum-base/topic', array('topicId' => $topic->id)
    );
    $paginationData = array(
        'currentPageNum' => $pageNum,
        'nbPages' => ceil($nbPostsTotal / $app->vars['forum-base.pagination.posts.nb_per_page']),
        'baseUrl' => $topicUrl . '?page=%page%'
    );

    // Breadcrumb management
    $parentForum = $topic->forum();
    $breadcrumbData = array($app->exec('utils.html.breadcrumb.get_home_part'));
    $breadcrumbData = array_merge($breadcrumbData, $app->exec('forum-base.html.breadcrumb.get_forum_part', $parentForum));
    $breadcrumbData[] = array(
        'url' => $topicUrl,
        'label' => 'core-plugins.forum-base.breadcrumb.topic',
        'labelParams' => array('%title%' => $topic->title),
    );

    return $app->get('view')->render('forum-base::topic-display',
        array(
            'topic' => $topic,
            'posts' => $postsToDisplay,
            'nbPostsTotal' => $nbPostsTotal,
            'paginationData' => $paginationData,
            'breadcrumbData' => $breadcrumbData,
        )
    );
};

return $action;