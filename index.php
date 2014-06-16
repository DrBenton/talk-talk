<?php

call_user_func(
    function () {

        // Go! Go! Go!
        $appInitClosure = require_once __DIR__ . '/app/boot/app.php';
        $app = call_user_func($appInitClosure);
        $app->run();

        // Some debug info
        if ($app->vars['debug'] && !$app->vars['isAjax']) {
            echo PHP_EOL . '<p>Duration: <b>' . round((microtime(true) - $app->vars['perfs.start_time']) * 1000) . '</b>ms.</p>' . PHP_EOL ;
            echo PHP_EOL . '<p>Max memory usage: <b>' . round(memory_get_peak_usage()/1000000, 3) . '</b>Mo.</p>' . PHP_EOL ;
            if (isset($_SESSION)) {
                echo PHP_EOL . '<div>$_SESSION: <pre>' . print_r($_SESSION, true) . '</pre></div>' . PHP_EOL ;
            }
            echo PHP_EOL . '<div><b>'.count(get_included_files()).'</b> included files: <pre>' . print_r(get_included_files(), true) . '</pre></div>' . PHP_EOL;
        }

    }
);
