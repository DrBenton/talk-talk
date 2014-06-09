<?php

$action = function ($batchIndex) use ($app) {

    $nbUsersToCreate = $app->vars['phpbb.import.users.nb_items_per_batch'];
    $from = $batchIndex * $nbUsersToCreate;

    // Disable SQL queries: that would be too much! :-)
    $app->vars['config']['debug']['perfs.tracking.sql_queries.enabled'] = false;

    // Import!
    $nbUsersCreated = $app->exec('phpbb.import.users.trigger_batch', $nbUsersToCreate, $from);

    if ($nbUsersCreated === $nbUsersToCreate) {
        // Seems that we still have users to create
        $importDone = false;
    } else {
        $importDone = true;
    }

    return $app->json(
        array(
            'batchIndex' => $batchIndex,
            'created' => $nbUsersCreated,
            'done' => $importDone,
            'duration' => $app->get('perfs')->getElapsedTime(),
        )
    );
};

return $action;