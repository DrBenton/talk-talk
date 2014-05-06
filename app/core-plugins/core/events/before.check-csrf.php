<?php

/**
 * @see app/core-plugins/core/services/csrf.php for CSRF token generation, related to this app event
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Util\StringUtils;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

$CSRF_PROTECTED_METHODS = array('POST', 'PUT', 'DELETE');

$app->before(
    function(Request $request) use ($app, $CSRF_PROTECTED_METHODS) {
        
        if (!in_array($request->getMethod(), $CSRF_PROTECTED_METHODS)) {
            return;//not a CSRF-protected HTTP method
        }
        
        // Let's check CSRF token!!
        $tokenName = (string)$app['config']['security']['csrf.token_name'];
        $receivedToken = $request->request->get($tokenName);
        $sessionToken = $app['session']->get($tokenName, '**no CSRF token in session**');
        
        if (!StringUtils::equals($sessionToken, $receivedToken)) {
            if ($app['debug']) {
                $errMsg = sprintf('Received CSRF token value "%s" does not match "%s"!', $receivedToken, $sessionToken);
            } else {
                $errMsg = null;
            }
            throw new AccessDeniedException($errMsg);
        }
    }
);