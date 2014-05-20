<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\User;

$action = function (Application $app, Request $request) {
    $breadcrumb = array(
        $app['utils.html.breadcrumb.home'],
        array(
            'url' => $app['url_generator']->generate('auth/sign-in'),
            'label' => 'core-plugins.auth.sign-in.breadcrumb.0',
        ),
    );

    return $app['twig']->render(
        'auth/sign-in/sign-in.form.twig',
        array(
            'user' => $request->get('user', new User),
            'breadcrumb' => $breadcrumb,
        )
    );
};

return $action;
