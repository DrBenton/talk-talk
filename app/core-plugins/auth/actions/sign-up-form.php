<?php

use TalkTalk\Model\User;

$action = function () use ($app) {

    $breadcrumbData = array(
        $app->exec('utils.html.breadcrumb.get_home_part'),
        array(
            'url' => $app->path('auth/sign-up'),
            'label' => 'core-plugins.auth.sign-up.breadcrumb.0',
        ),
    );

    if ($returnUrl = $app->getRequest()->get('return-url')) {
        $app->get('session')->set('url.intended', $returnUrl);
    }

    return $app->get('view')->render(
        'auth::sign-up/sign-up.form',
        array(
            'user' => $app->getRequest()->get('user', new User),
            'breadcrumbData' => $breadcrumbData,
        )
    );
};

return $action;
