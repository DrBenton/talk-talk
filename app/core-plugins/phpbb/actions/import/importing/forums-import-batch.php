<?php

$action = function ($batchIndex) use ($app) {

    $nbForumsToCreate = $app->vars['phpbb.import.forums.nb_items_per_batch'];
    $from = $batchIndex * $nbForumsToCreate;

    // Disable SQL queries: that would be too much! :-)
    $app->vars['config']['debug']['perfs.tracking.sql_queries.enabled'] = false;

    // Import!
    $nbForumsCreated = $app->exec('phpbb.import.forums.trigger_batch', $nbForumsToCreate, $from);

    if ($nbForumsCreated === $nbForumsToCreate) {
        // Seems that we still have users to create
        $importDone = false;
    } else {
        $importDone = true;
    }

    return $app->json(
        array(
            'batchIndex' => $batchIndex,
            'created' => $nbForumsCreated,
            'done' => $importDone,
            'duration' => $app->get('perfs')->getElapsedTime(),
        )
    );
};

return $action;