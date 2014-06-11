<?php

use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\Post;
use TalkTalk\Model\Topic;

$action = function (Request $request, Topic $topic) use ($app) {

    $topic->load('author');

    $topicUrl = $app->path(
        'forum-base/topic', array('topic' => $topic->id)
    );

    // First & last Posts
    $firstPost = $topic->firstPost;
    $lastPosts = $topic->lastPosts(3)->getResults()->all();

    // Don't display the first Post in the last Posts list
    $lastPosts = array_filter($lastPosts, function (Post $post) use ($firstPost) {
        return $firstPost->id != $post->id;
    });

    // Breadcrumb management
    $parentForum = $topic->forum();
    $breadcrumbData = array($app->exec('utils.html.breadcrumb.get_home_part'));
    $breadcrumbData = array_merge($breadcrumbData, $app->exec('forum-base.html.breadcrumb.get_forum_part', $parentForum));
    $breadcrumbData[] = array(
        'url' => $topicUrl,
        'label' => 'core-plugins.forum-base.breadcrumb.topic',
        'labelParams' => array('%title%' => $topic->title),
    );
    $breadcrumbData[] = array(
        'url' => $app->path('forum-base/new-post-form', array('topic' => $topic->id)),
        'label' => 'core-plugins.forum-base.breadcrumb.new_post'
    );

    $post = $request->get('post');
    if (null === $post) {
        $post = new Post(array(
            'title' => $app->get('translator')->trans(
                'core-plugins.forum-base.new-post.form.title-default-content',
                array('%topic-title%' => $topic->title)
            )
        ));
    }

    return $app->get('view')->render(
        'forum-base::new-post-page',
        array(
            'topic' => $topic,
            'post' => $post,
            'firstPost' => $firstPost,
            'lastPosts' => $lastPosts,
            'breadcrumbData' => $breadcrumbData,
        )
    );
};

return $action;