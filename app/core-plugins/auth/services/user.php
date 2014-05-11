<?php

use TalkTalk\Model\User;

$app['user'] = $app->share(
    function () use ($app) {
        if ($app['isAnonymous']) {
            return null;
        }

        return User::find($app['session']->get('userId'));
    }
);
