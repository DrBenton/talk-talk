<?php

use TalkTalk\Model\User;

$action = function () use ($app, &$showFormOnError) {

    // Get form User data
    $userData = $app->vars['request']->post('user');

    // Do we have a User with such a login?
    $dbUser = User::where('login', '=', $userData['login'])->first();
    if (null === $dbUser) {
        return $showFormOnError($app, $userData);
    }

    // Well, we have one! Is it the right password?
    // --> we trigger the 'auth.user.check-signin-credentials' hook!
    $passwordSuccesses = $app->getService('hooks')->triggerPluginsHook(
        'auth.user.check-signin-credentials',
        array($userData, $dbUser)
    );

    if (!$app->getService('utils.array')->containsTrue($passwordSuccesses)) {
        // No Plugin responded "true" for this hook: these User credentials are not correct!
        return $showFormOnError($app, $userData);
    }

    // All right, our User is correctly authentified! Let's log him/her...
    $app->getService('session')->set('userId', $dbUser->id);

    // Success feedback
    $app->getService('flash')->flashTranslated(
        'success',
        'core-plugins.auth.sign-in.notifications.success',
        array('%login%' => $dbUser->login)
    );

    if ($app['isAjax']) {
        // JS response
        return $app->getService('view')->render(
            'auth/sign-in/sign-in.success.ajax.twig',
            array('user' => $dbUser)
        );
    } else {
        // Redirection to home / intended URL, with flashed notification
        $targetUrl = $app->getService('session')->get(
            'url.intended',
            $app->path('core/home')
        );
        $app->getService('session')->clear('url.intended');

        return $app->redirect($targetUrl);
    }
};

$showFormOnError = function ($app, array $userData) {
    $app->getService('flash')->flashTranslated(
        'error',
        'core-plugins.auth.sign-in.notifications.error'
    );

    return $app->getService('view')->render(
        'auth::sign-in/sign-in.form',
        array(
            'user' => new User($userData)
        )
    );
};

return $action;