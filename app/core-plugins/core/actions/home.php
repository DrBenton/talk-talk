<?php

$action = function () use ($app) {
    return $app->getService('view')
        ->render('core::home');
};

return $action;