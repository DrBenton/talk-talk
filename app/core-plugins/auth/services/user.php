<?php

use TalkTalk\Model\User;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;

$app['user'] = $app->share(
    function () use ($app) {
        if ($app['isAnonymous']) {
            throw new AuthenticationServiceException('No authenticated User found!');
        }

        return User::findOrFail($app['session']->get('userId'));
    }
);
