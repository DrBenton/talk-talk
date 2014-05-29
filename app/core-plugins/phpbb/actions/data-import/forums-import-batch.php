<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$action = function (Application $app, Request $request, $batchIndex) {

    $nbForumsToCreate = $app['phpbb.import.forums.nb_items_per_batch'];
    $from = $batchIndex * $nbForumsToCreate;

    // Disable SQL queries: that would be too much! :-)
    $app['config.set']('debug/perfs.tracking.sql_queries.enabled', false);

    // Import!
    $nbForumsCreated = $app['phpbb.import.forums.trigger_batch']($nbForumsToCreate, $from);

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
            'duration' => $app['perfs.now.time_elapsed'],
            'done' => $importDone,
        )
    );
};

return $action;
