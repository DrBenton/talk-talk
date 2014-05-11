<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$NB_USERS_PER_RANGE = 100;

$action = function (Application $app, Request $request, $batchIndex) use ($NB_USERS_PER_RANGE) {

    $nbUsersToCreate = $NB_USERS_PER_RANGE;
    $from = $batchIndex * $NB_USERS_PER_RANGE;
    $nbUsersCreated = $app['phpbb.import.import-users']($nbUsersToCreate, $from);

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
