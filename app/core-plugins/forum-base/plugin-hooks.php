<?php

use TalkTalk\Model\Post;

$hooks['post.handle_content'] = function (Post &$post) use ($app) {
    // Let's handle the Post "content" field with our "markup manager"!
    $post->title = $app['forum-base.markup-manager.handle_forum_markup.all']($post->title);
    $post->content = $app['forum-base.markup-manager.handle_forum_markup.all']($post->content);
};
