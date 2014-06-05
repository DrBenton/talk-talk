<?php

$action = function () use ($app) {
    return $app->get('view')
        ->render('core::home');
};

return $action;
