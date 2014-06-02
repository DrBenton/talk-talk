<?php

call_user_func(
    function () {

        // Go! Go! Go!
        $appInitClosure = require_once __DIR__ . '/app/boot/app.php';
        $app = call_user_func($appInitClosure);
        $app->run();

    }
);
