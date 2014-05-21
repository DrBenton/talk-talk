<?php

use Silex\Application;
use TalkTalk\Model\User;
use TalkTalk\Model\Forum;
use TalkTalk\Model\Topic;
use TalkTalk\Model\Post;

$action = function (Application $app) {

    // Okay, let's clear the previously created DB entities
    $nbPostsDeleted = Post::where('provider', '=', $app['phpbb.import.provider.name'])->delete();
    $nbTopicsDeleted = Topic::where('provider', '=', $app['phpbb.import.provider.name'])->delete();
    $nbForumsDeleted = Forum::where('provider', '=', $app['phpbb.import.provider.name'])->delete();
    $nbUsersDeleted = User::where('provider', '=', $app['phpbb.import.provider.name'])->delete();

    return $app->json(
        array(
            'success' => true,
            'nbPostsDeleted' => $nbPostsDeleted,
            'nbTopicsDeleted' => $nbTopicsDeleted,
            'nbForumsDeleted' => $nbForumsDeleted,
            'nbUsersDeleted' => $nbUsersDeleted,
        )
    );
};

return $action;
