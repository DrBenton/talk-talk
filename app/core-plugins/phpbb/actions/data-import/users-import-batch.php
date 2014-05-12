<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$action = function (Application $app, Request $request, $batchIndex) {

    $nbUsersToCreate = $app['phpbb.import.users.nb_items_per_batch'];
    $from = $batchIndex * $nbUsersToCreate;
    $nbUsersCreated = $app['phpbb.import.users.trigger_batch']($nbUsersToCreate, $from);

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
            'duration' => $app['perfs.script.duration'],
            'done' => $importDone,
        )
    );
};

return $action;
