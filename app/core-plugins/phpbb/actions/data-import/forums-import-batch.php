<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$action = function (Application $app, Request $request, $batchIndex) {

    // Because of "parent_id" ids mapping, we have to process forums in a single batch
    $nbForumsCreated = $app['phpbb.import.forums.trigger_batch']();

    return $app->json(
        array(
            'batchIndex' => $batchIndex,
            'created' => $nbForumsCreated,
            'duration' => $app['perfs.script.duration'],
            'done' => true,
        )
    );
};

return $action;
