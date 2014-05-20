<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\User;

$action = function (Application $app, Request $request) {
    $breadcrumb = array(
        $app['utils.html.breadcrumb.home'],
        array(
            'url' => $app['url_generator']->generate('auth/sign-up'),
            'label' => 'core-plugins.auth.sign-up.breadcrumb.0',
        ),
    );

    return $app['twig']->render(
        'auth/sign-up/sign-up.form.twig',
        array(
            'user' => $request->get('user', new User),
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;
