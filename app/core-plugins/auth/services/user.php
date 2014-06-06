<?php

use TalkTalk\Model\User;

$app->defineService(
    'user',
    function () use ($app) {
        if ($app->vars['isAnonymous']) {
            throw new \DomainException('No authenticated User found!');
        }

        return User::findOrFail($app->get('session')->get('userId'));
    }
);
