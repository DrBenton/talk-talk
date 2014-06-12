<?php

use Symfony\Component\HttpFoundation\Response;

$firewall = function () use ($app) {
    if ($app->get('user')->isAnonymous()) {

        // We store this URL: if the User successfully authenticates afterwards,
        // we will redirect him/her to this URL
        if ('GET' === $app->getRequest()->getMethod()) {
            $currentUrl = $app->vars['app.base_url'] . $app->getRequest()->getPathInfo();
            $app->get('session')->set('url.intended', $currentUrl);
        }

        $httpStatusCode = 401;
        $app->vars['app.http_status_code'] = $httpStatusCode;

        $app->get('flash')->flashTranslated(
            'alerts.info.authentication-required',
            'core-plugins.auth.middlewares.authentication-required',
            array()
        );

        // Let's display the "sign in" form in place of the required page,
        // with a HTTP "Forbidden" status!
        $signInFormHtml = $app->exec('actions.run', 'app/core-plugins/auth/actions/sign-in-form');

        return new Response($signInFormHtml, $httpStatusCode);
    }
};

return $firewall;
