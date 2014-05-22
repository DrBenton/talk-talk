<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Core\Utils\ArrayUtils;
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
    // --> we trigger the 'auth.user.check-signin-credentials' hook!
    $passwordSuccesses = $app['plugins.trigger_hook'](
        'auth.user.check-signin-credentials',
        array($userData, $dbUser)
    );

    if (!ArrayUtils::containsTrue($passwordSuccesses)) {
        // No Plugin responded "true" for this hook: these User credentials are not correct!
        return $showFormOnError($app, $userData);
    }

    // All right, our User is correctly authentified! Let's log him/her...
    $app['session']->set('userId', $dbUser->id);

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
        // Redirection to home / intended URL, with flashed notification
        $targetUrl = $app['session']->get(
            'url.intended',
            $app['url_generator']->generate('core/home')
        );
        $app['session']->remove('url.intended');
        return $app->redirect($targetUrl);
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
