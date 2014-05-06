<?php

/**
 * @see app/core-plugins/core/events/before.check-csrf.php for CSRF protection, related to this Service
 */

use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;

$app['csrf.token_name'] = $app->share(
    function ($app) {
        return (string) $app['config']['security']['csrf.token_name'];
    }
);

$app['csrf.token_value'] = $app->share(
    function ($app) {

        if ($app['session']->has($app['csrf.token_name'])) {
            return $app['session']->get($app['csrf.token_name']);
        }

        // Token creation
        $tokenGenerator = new UriSafeTokenGenerator();
        $token = $tokenGenerator->generateToken();
        // The generated token is automatically copied to our Session data
        $tokenName = $app['csrf.token_name'];
        $app['session']->set($tokenName, $token);

        return $token;
    }
);
