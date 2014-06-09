<?php

use TalkTalk\Model\User;

$action = function () use ($app, &$getFormValidator) {

    // Get form User data
    $userData = $app->getRequest()->request->get('user');

    // Validate!
    $validator = $getFormValidator($app, $userData);

    if ($validator->fails()) {
        // Flash errors & redirect if validation failed
        $app->exec('validator.flash_validator_messages', $validator);

        return $app->get('view')->render(
            'auth::sign-up/sign-up.form',
            array(
                'user' => new User($userData),
                'failed_fields' => $validator->failed()
            )
        );
    }

    // This is why we need PHP 5.3.7+ ...
    $userData['password'] = $app->get('crypto')->hashPassword($userData['password']);

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
    $app->get('session')->set('userId', $user->id);

    // Success feedback
    $app->get('flash')->flashTranslated(
        'alerts.success.sign-in',
        'core-plugins.auth.sign-up.notifications.success',
        array('%login%' => $user->login)
    );

    if ($app->vars['isAjax']) {
        // JS response
        return $app->get('view')->render(
            'auth::sign-up/sign-up-target.success.ajax',
            array('user' => $user)
        );
    } else {
        // Redirection to home / intended URL, with flashed notification
        $targetUrl = $app->get('session')->get(
            'url.intended',
            $app->path('core/home')
        );
        $app->get('session')->remove('url.intended');

        return $app->redirect($targetUrl);
    }
};

$getFormValidator = function ($app, array $userData) {
    $validator = $app->exec(
        'validator.get',
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
