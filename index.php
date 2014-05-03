<?php

use Symfony\Component\HttpFoundation\Request;

call_user_func(
    function () {

        $vendorsPath = __DIR__ . '/vendor/php';

        // Composer init
        require_once $vendorsPath . '/autoload.php';

        // Go! Go! Go!
        $request = Request::createFromGlobals();
        $appInitClosure = require_once __DIR__ . '/app/bootstrap/app.php';
        $app = $appInitClosure($request);
        $app->run($request);

    }
);
