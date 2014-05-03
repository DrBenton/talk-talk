<?php

$action = function (\Silex\Application $app) {
    if (!$app['debug']) {
        return $app->abort(500, 'PHP info is forbidden on this server.');
    }

    ob_start();
    phpinfo();

    return ob_get_flush();
};

return $action;
