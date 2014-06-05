<?php

$app->before(
    function () use ($app) {
        $app->vars['isAuthenticated'] = $app->get('session')->has('userId');
        $app->vars['isAnonymous'] = !$app->vars['isAuthenticated'];
    },
    100
);
