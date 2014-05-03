<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\User;

$action = function (Application $app, Request $request) {
    return $app['twig']->render(
        'auth/sign-up/sign-up.form.twig',
        array(
            'user' => $request->get('user', new User)
        )
    );
};

return $action;
