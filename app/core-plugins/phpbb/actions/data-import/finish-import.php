<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$action = function (Application $app, Request $request) {

    // Okay, let's remove these heavy Session vars
    foreach (array('users', 'forums', 'topics', 'post') as $importType) {
        $app['session']->remove('phpbb.import.' . $importType . '.ids_mapping');
    }

    return $app->json(
        array(
            'success' => true,
        )
    );
};

return $action;
