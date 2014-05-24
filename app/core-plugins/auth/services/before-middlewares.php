<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

$app['auth.middleware.is-authenticated'] = $app->protect(
    function () use ($app) {
        if ($app['isAnonymous']) {

            // We store this URL: if the User successfully authenticates afterwards,
            // we will redirect him/her to this URL
            if ('GET' === $app['request']->getMethod()) {
                $currentUrl = $app['request']->getPathInfo();
                $app['session']->set('url.intended', $currentUrl);
            }

            $httpStatusCode = 401;
            $app['app.http_status_code'] = $httpStatusCode;

            $app['session.flash.add.translated'](
                'core-plugins.auth.middlewares.authentication-required',
                array(),
                'info'
            );

            // Let's display the "sign in" form in place of the required page,
            // with a HTTP "Forbidden" status!
            $signInFormHtml = $app['plugins.manager']->runActionFile(
                'app/core-plugins/auth/actions/sign-in-form'
            );

            return new Response($signInFormHtml, $httpStatusCode);
        }
    }
);

$app['auth.middleware.is-anonymous'] = $app->protect(
    function () use ($app) {
        if ($app['isAuthenticated']) {
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
