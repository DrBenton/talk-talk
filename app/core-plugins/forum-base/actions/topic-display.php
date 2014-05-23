<?php

use TalkTalk\Model\Topic;
use TalkTalk\Model\Post;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$action = function (Application $app, Request $request, $topicId) {

    $topic = Topic::findOrFail($topicId);
    $topic->load('author');

    // Posts retrieval (only those of the current page)
    $pageNum = $request->query->getInt('page', 1);
    $posts = $topic->posts();
    $postsToDisplay = $posts->getQuery()
        ->orderBy('created_at', 'ASC')
        ->forPage(
            $pageNum,
            $app['forum-base.pagination.posts.nb_per_page']
        )
        ->get()
        ->load('author') /* "author" eager loading */
        ->all();

    /**
     * Check eager loading with MySQL queries logs :-)
     * @see http://stackoverflow.com/questions/650238/how-to-show-the-last-queries-executed-on-mysql
     */

    // We run the "post.handle_content" hook for each Post!
    // This will convert our Posts bbcode and specific markups to good ol' HTML
    array_walk(
        $postsToDisplay,
        function (Post &$post) use ($app) {
            $app['plugins.trigger_hook']('post.handle_content', array(&$post));
        }
    );

    // Total number of posts retrieval
    $nbPostsTotal = Post::where('topic_id', '=', $topic->id)->count();

    // Pagination stuff
    $topicUrl = $app['url_generator']->generate(
        'forum-base/topic', array('topicId' => $topic->id)
    );
    $paginationData = array(
        'currentPageNum' => $pageNum,
        'nbPages' => ceil($nbPostsTotal / $app['forum-base.pagination.posts.nb_per_page']),
        'baseUrl' => $topicUrl . '?page=%page%'
    );

    // Breadcrumb management
    $parentForum = $topic->forum();
    $breadcrumb = array($app['utils.html.breadcrumb.home']);
    $breadcrumb = array_merge($breadcrumb, $app['forum-base.html.breadcrumb.get_forum_part']($parentForum));
    $breadcrumb[] = array(
        'url' => $topicUrl,
        'label' => 'core-plugins.forum-base.breadcrumb.topic',
        'labelParams' => array('%name%' => $topic->name),
    );

    return $app['twig']->render('forum-base/topic-display.twig',
        array(
            'topic' => $topic,
            'posts' => $postsToDisplay,
            'nbPostsTotal' => $nbPostsTotal,
            'paginationData' => $paginationData,
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;
