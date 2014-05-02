<?php

call_user_func(
    function () {
        
        $vendorsPath = __DIR__ . '/vendor/php';
        
        // Composer init
        require_once $vendorsPath . '/autoload.php';
        
        // Go! Go! Go!
        $app = require_once __DIR__ . '/app/bootstrap/app.php';
        $app->run();
        
    }
);
