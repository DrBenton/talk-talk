<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\User;

$action = function (Application $app, Request $request) use (&$showFormOnError) {

    // Get form User data
    $userData = $request->get('user');

    // Do we have a User with such a login?
    $dbUser = User::where('login', '=', $userData['login'])->first();
    if (null === $dbUser) {
        return $showFormOnError($app, $userData);
    }

    // Well, we have one! Is it the right password?
    $passwordSuccess = $app['crypt.password.verify']($userData['password'], $dbUser->password);
    if (false === $passwordSuccess) {
        return $showFormOnError($app, $userData);
    }

    // All right, our User is correctly authentified! Let's log him/her...
    $app['session']->set('user', $dbUser->toArray());

    // Success feedback
    $app['session.flash.add.translated'](
        'core-plugins.auth.sign-in.notifications.success',
        array('%login%' => $dbUser->login),
        'success'
    );

    if ($app['isAjax']) {
        // JS response
        return $app['twig']->render(
            'auth/sign-in/sign-in.success.ajax.twig',
            array('user' => $dbUser)
        );
    } else {
        // Redirection to home, with flashed notification
        return $app->redirect($app['url_generator']->generate('core/home'));
    }
};

$showFormOnError = function (Application $app, $userData) {
    $app['session.flash.add.translated']('core-plugins.auth.sign-in.notifications.error', array(), 'error');

    return $app['twig']->render(
        'auth/sign-in/sign-in.form.twig',
        array(
            'user' => new User($userData)
        )
    );
};

return $action;