<?php

use TalkTalk\Model\User;

$action = function () use ($app) {
    /*
    $breadcrumb = array(
        $app['utils.html.breadcrumb.home'],
        array(
            'url' => $app['url_generator']->generate('auth/sign-up'),
            'label' => 'core-plugins.auth.sign-up.breadcrumb.0',
        ),
    );
    */
    $breadcrumb = array();

    if ($returnUrl = $app->vars['request']->get('return-url')) {
        $app->getService('session')->set('url.intended', $returnUrl);
    }

    return $app->getService('view')->render(
        'auth::sign-up/sign-up.form',
        array(
            'user' => $app->vars['request']->get('user', new User),
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;