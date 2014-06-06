<?php

$action = function () use ($app) {

    $app->get('session')->remove('userId');

    $app->get('flash')->flashTranslated(
        'alerts.info.sign-out',
        'core-plugins.auth.sign-out.notifications.success'
    );

    if ($app->vars['isAjax']) {
        // JS response
        return $app->get('view')->render(
            'auth::sign-out/sign-out.success.ajax'
        );
    } else {
        // Redirection to home
        return $app->redirectToAction('core/home');
    }
};

return $action;
