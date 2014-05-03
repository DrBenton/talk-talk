<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\User;

$action = function (Application $app, Request $request) {
    
    // Get form User data
    $userData = $request->get('user');
    
    // Validate!
    $validator = $app['auth.helpers.sign-up.get-form-validator']($userData);

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
    $userData['password'] = $app['crypt.password.hash']($userData['password']);

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
    $app['session']->set('user', $user->toArray());

    // Success feedback
    $app['session.flash.add.translated'](
        'core-plugins.auth.sign-up.notifications.success',
        array('%login%' => $user->login),
        'success'
    );
    
    if ($app['isAjax']) {
        // JS response
        return $app['twig']->render(
            'core/auth/sign-in/sign-up.success.twig',
            array('user' => $user)
        );
    } else {
        // Redirection to home, with flashed notification
        return $app->redirect('/');
    }
};

return $action;
