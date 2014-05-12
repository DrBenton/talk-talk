<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$action = function (Application $app, Request $request) {
    return $app->json(
        $app['phpbb.import.users.metadata']
    );
};

return $action;
