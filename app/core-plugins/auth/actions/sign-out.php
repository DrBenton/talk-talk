<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\User;

$action = function (Application $app, Request $request) {

    $app['session']->remove('user');

    $app['session.flash.add.translated']('core-plugins.auth.sign-out.notifications.success', array(), 'info');


    if ($app['isAjax']) {
        // JS response
        return $app['twig']->render(
            'auth/sign-out/sign-out.success.ajax.twig'
        );
    } else {
        // Redirection to home
        return $app->redirect($app['url_generator']->generate('core/home'));
    }
};

return $action;
