<?php

use Symfony\Component\Security\Core\Exception\AuthenticationException;

$app['auth.middleware.is-authenticated'] = $app->protect(
    function () use ($app) {
        if (!$app['session']->has('user')) {
            // We store this URL: if the User successfully authenticates afterwards,
            // we will redirect him/her to this URL
            if ('GET' === $app['request']->getMethod()) {
                $currentUrl = $app['request']->getPathInfo();
                $app['session']->set('url.intended', $currentUrl);
            }
            return $app->redirect(
              $app['url_generator']->generate('auth/sign-in')
            );
            //throw new AuthenticationException('You must be authenticated to access this resource!');
        }
    }
);

$app['auth.middleware.is-anonymous'] = $app->protect(
    function () use ($app) {
        if ($app['session']->has('user')) {
            throw new AuthenticationException('You must not be authenticated to access this resource!');
        }
    }
);

/*
//TODO: Users ACL
$app['auth.middleware.is-admin'] = $app->protect(
    function () use ($app) {
        if (!$app['session']->has('user')) {
            throw new AuthenticationException('You must be authenticated to access this resource!');
        }
    }
);
*/
