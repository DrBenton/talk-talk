<?php

/**
 * @see app/core-plugins/core/services/csrf.php for CSRF token generation, related to this app event
 */

use Symfony\Component\Security\Core\Util\StringUtils;

$CSRF_PROTECTED_METHODS = array('POST', 'PUT', 'DELETE');

$app->before(
    function () use ($app, $CSRF_PROTECTED_METHODS) {

        $request = $app->getRequest();

        if (!in_array($request->getMethod(), $CSRF_PROTECTED_METHODS)) {
            return; //not a CSRF-protected HTTP method
        }

        // Let's check CSRF token!!
        $tokenName = $app->get('csrf')->getTokenName();
        $receivedToken = $request->post($tokenName);
        if (null === $receivedToken && isset($request->headers['X-CSRF-Token'])) {
            $receivedToken = $request->headers['X-CSRF-Token'];
        }
        $sessionToken = $app->get('session')->get($tokenName, '**no CSRF token in session**');

        if (!StringUtils::equals($sessionToken, $receivedToken)) {
            if ($app->vars['debug']) {
                $errMsg = sprintf('Received CSRF token value "%s" does not match "%s"!', $receivedToken, $sessionToken);
            } else {
                $errMsg = null;
            }
            throw new \Exception($errMsg);
        }
    }
);