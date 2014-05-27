<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$action = function (Application $app, Request $request, $batchIndex) {

    $nbPostsToCreate = $app['phpbb.import.posts.nb_items_per_batch'];
    $from = $batchIndex * $nbPostsToCreate;
    $nbPostsCreated = $app['phpbb.import.posts.trigger_batch']($nbPostsToCreate, $from);

    if ($nbPostsCreated === $nbPostsToCreate) {
        // Seems that we still have posts to create
        $importDone = false;
    } else {
        $importDone = true;
    }

    return $app->json(
        array(
            'batchIndex' => $batchIndex,
            'created' => $nbPostsCreated,
            'duration' => $app['perfs.now.time_elapsed'],
            'done' => $importDone,
        )
    );
};

return $action;
