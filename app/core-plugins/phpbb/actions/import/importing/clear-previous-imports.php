<?php

use TalkTalk\Model\User;
use TalkTalk\Model\Forum;
use TalkTalk\Model\Topic;
use TalkTalk\Model\Post;

$action = function () use ($app) {

    $phpBbProviderName = $app->vars['phpbb.import.provider.name'];

    // Okay, let's clear the previously created DB entities
    $nbPostsDeleted = Post::where('provider', '=', $phpBbProviderName)->delete();
    $nbTopicsDeleted = Topic::where('provider', '=', $phpBbProviderName)->delete();
    $nbForumsDeleted = Forum::where('provider', '=', $phpBbProviderName)->delete();
    $nbUsersDeleted = User::where('provider', '=', $phpBbProviderName)->delete();

    // We also clear the previous imported DB entities ids mappings hashes (if any)
    $app->exec('phpbb.import.clear_imports_ids_mappings');

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