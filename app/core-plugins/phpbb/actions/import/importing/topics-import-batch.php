<?php

$action = function ($batchIndex) use ($app) {

    $nbTopicsToCreate = $app->vars['phpbb.import.topics.nb_items_per_batch'];
    $from = $batchIndex * $nbTopicsToCreate;

    // Disable SQL queries: that would be too much! :-)
    $app->vars['config']['debug']['perfs.tracking.sql_queries.enabled'] = false;

    // Import!
    $nbTopicsCreated = $app->exec('phpbb.import.topics.trigger_batch', $nbTopicsToCreate, $from);

    if ($nbTopicsCreated === $nbTopicsToCreate) {
        // Seems that we still have topics to create
        $importDone = false;
    } else {
        $importDone = true;
    }

    return $app->json(
        array(
            'batchIndex' => $batchIndex,
            'created' => $nbTopicsCreated,
            'done' => $importDone,
            'duration' => $app->get('perfs')->getElapsedTime(),
        )
    );
};

return $action;