<?php

$action = function () use ($app) {
    ob_start();

    phpinfo();

    $app->getResponse()->setBody(ob_get_flush());
};

return $action;
