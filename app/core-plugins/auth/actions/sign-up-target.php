<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\User;

$action = function (Application $app, Request $request) use (&$getFormValidator) {

    // Get form User data
    $userData = $request->get('user');

    // Validate!
    $validator = $getFormValidator($app, $userData);

    if ($validator->fails()) {
        // Flash errors & redirect if validation failed
        $app['validator.flash_validator_messages']($validator);

        return $app['twig']->render(
            'auth/sign-up/sign-up.form.twig',
            array(
                'user' => new User($userData),
                'failed_fields' => $validator->failed()
            )
        );
    }

    // This is why we need PHP 5.3.7+ ...
    $userData['password'] = $app['crypto.password.hash']($userData['password']);

    // Model setup
    $user = new User();
    $user->login = $userData['login'];
    $user->password = $userData['password'];
    $user->email = $userData['email'];
    $user->provider = 'talk-talk';
    $user->provider_version = 0.1;
    // Save!
    $user->save();

    // Our new User is automatically logged
    $app['session']->set('userId', $user->id);

    // Success feedback
    $app['session.flash.add.translated'](
        'core-plugins.auth.sign-up.notifications.success',
        array('%login%' => $user->login),
        'success'
    );

    if ($app['isAjax']) {
        // JS response
        return $app['twig']->render(
            'auth/sign-up/sign-up.success.ajax.twig',
            array('user' => $user)
        );
    } else {
        // Redirection to home, with flashed notification
        return $app->redirect($app['url_generator']->generate('core/home'));
    }
};

$getFormValidator = function (Application $app, array $userData) {
    $validator = $app['validator.get'](
        $userData,
        array(
            'login' => 'required|min:3|unique:users',
            'password' => 'required|confirmed|min:3',
            'email' => 'required|email|unique:users',
        )
    );

    return $validator;
};

return $action;
