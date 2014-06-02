<?php

call_user_func(
    function () {

        // Go! Go! Go!
        $appInitClosure = require_once __DIR__ . '/app/boot/app.php';
        $app = call_user_func($appInitClosure);
        $app->run();

        echo PHP_EOL . '<p><b>' . round(microtime(true) - $app->vars['perfs.start_time'], 3) . '</b>s.</p>' . PHP_EOL ;
        echo PHP_EOL . '<pre>' . print_r(get_included_files(), true) . '</pre>';

    }
);
