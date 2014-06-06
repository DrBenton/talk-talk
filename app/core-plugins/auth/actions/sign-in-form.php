<?php

use TalkTalk\Model\User;

$action = function () use ($app) {
    /*
    $breadcrumb = array(
        $app['utils.html.breadcrumb.home'],
        array(
            'url' => $app['url_generator']->generate('auth/sign-in'),
            'label' => 'core-plugins.auth.sign-in.breadcrumb.0',
        ),
    );
    */
    $breadcrumb = array();

    if ($returnUrl = $app->vars['request']->get('return-url')) {
        $app->get('session')->set('url.intended', $returnUrl);
    }

    return $app->get('view')->render(
        'auth::sign-in/sign-in.form',
        array(
            'user' => $app->vars['request']->get('user', new User),
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;
