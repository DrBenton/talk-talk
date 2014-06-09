<?php

$action = function ($batchIndex) use ($app) {

    $nbPostsToCreate = $app->vars['phpbb.import.posts.nb_items_per_batch'];
    $from = $batchIndex * $nbPostsToCreate;

    // Disable SQL queries: that would be too much! :-)
    $app->vars['config']['debug']['perfs.tracking.sql_queries.enabled'] = false;

    // Import!
    $nbPostsCreated = $app->exec('phpbb.import.posts.trigger_batch', $nbPostsToCreate, $from);

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
            'done' => $importDone,
            'duration' => $app->get('perfs')->getElapsedTime(),
        )
    );
};

return $action;