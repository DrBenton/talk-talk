<?php

use TalkTalk\Model\User;
use Seyon\PHPBB3\UserBundle\Security\Encoder;

$hooks['auth.user.check-signin-credentials'] = function ($submittedUserData, User $dbUser) use ($app) {

    $app->get('logger')->debug('auth.user.check-signin-credentials hook of phpbb.');
    if ('phpbb-import' !== $dbUser->provider) {
        // This user is handled by another "user provider"; let's stop here...
        return false;
    }

    $submittedPassword = $submittedUserData['password'];
    $dbUserPassword = $dbUser->password;

    $phpBbPasswordEncoder = new Encoder();

    return $phpBbPasswordEncoder->isPasswordValid($dbUserPassword, $submittedPassword);
};