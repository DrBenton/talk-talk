<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$action = function (Application $app, Request $request, $batchIndex) {

    $nbTopicsToCreate = $app['phpbb.import.topics.nb_items_per_batch'];
    $from = $batchIndex * $nbTopicsToCreate;
    $nbTopicsCreated = $app['phpbb.import.topics.trigger_batch']($nbTopicsToCreate, $from);

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
            'duration' => $app['perfs.script.duration'],
            'done' => $importDone,
        )
    );
};

return $action;
