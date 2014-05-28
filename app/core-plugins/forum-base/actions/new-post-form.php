<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\Post;
use TalkTalk\Model\Topic;

$action = function (Application $app, Request $request, Topic $topic) {

    $topic->load('author');

    $topicUrl = $app['url_generator']->generate(
        'forum-base/topic', array('topic' => $topic->id)
    );

    // First & last Posts
    $firstPost = $topic->firstPost();
    $lastPosts = $topic->lastPosts(3);

    // Don't display the first Post in the last Posts list
    $lastPosts = array_filter($lastPosts, function (Post $post) use ($firstPost) {
        return $firstPost->id != $post->id;
    });

    // Breadcrumb management
    $parentForum = $topic->forum();
    $breadcrumb = array($app['utils.html.breadcrumb.home']);
    $breadcrumb = array_merge($breadcrumb, $app['forum-base.html.breadcrumb.get_forum_part']($parentForum));
    $breadcrumb[] = array(
        'url' => $topicUrl,
        'label' => 'core-plugins.forum-base.breadcrumb.topic',
        'labelParams' => array('%title%' => $topic->title),
    );
    $breadcrumb[] = array(
        'url' => $app['url_generator']->generate('forum-base/new-post-form', array('topic' => $topic->id)),
        'label' => 'core-plugins.forum-base.breadcrumb.new_post'
    );

    $post = $request->get('post');
    if (null === $post) {
        $post = new Post(array(
            'title' => $app['translator']->trans(
                'core-plugins.forum-base.new-post.form.title-default-content',
                array('%topic-title%' => $topic->title)
            )
        ));
    }

    return $app['twig']->render(
        'forum-base/new-post-form.twig',
        array(
            'topic' => $topic,
            'post' => $post,
            'firstPost' => $firstPost,
            'lastPosts' => $lastPosts,
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;
