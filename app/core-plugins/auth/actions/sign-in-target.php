<?php

use TalkTalk\Model\User;

$action = function () use ($app, &$showFormOnError) {

    // Get form User data
    $userData = $app->getRequest()->request->get('user');

    // Do we have a User with such a login?
    $dbUser = User::where('login', '=', $userData['login'])->first();
    if (null === $dbUser) {
        return $showFormOnError($app, $userData);
    }

    // Well, we have one! Is it the right password?
    // --> we trigger the 'auth.user.check-signin-credentials' hook!
    $passwordSuccesses = $app->get('hooks')->triggerPluginsHook(
        'auth.user.check-signin-credentials',
        array($userData, $dbUser)
    );

    $app->get('logger')->debug('$passwordSuccesses='.json_encode($passwordSuccesses));
    if (!$app->get('utils.array')->containsTrue($passwordSuccesses)) {
        // No Plugin responded "true" for this hook: these User credentials are not correct!
        return $showFormOnError($app, $userData);
    }

    // All right, our User is correctly authenticated! Let's log him/her...
    $app->get('session')->set('userId', $dbUser->id);

    // Success feedback
    $app->get('flash')->flashTranslated(
        'alerts.success.sign-in',
        'core-plugins.auth.sign-in.notifications.success',
        array('%login%' => $dbUser->login)
    );

    if ($app->vars['isAjax']) {
        // JS response
        return $app->get('view')->render(
            'auth::sign-in/sign-in-target.success.ajax',
            array('user' => $dbUser)
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

$showFormOnError = function ($app, array $userData) {
    $app->get('flash')->flashTranslated(
        'alerts.error.sign-in',
        'core-plugins.auth.sign-in.notifications.error'
    );

    return $app->get('view')->render(
        'auth::sign-in/sign-in.form',
        array(
            'user' => new User($userData)
        )
    );
};

return $action;
