<?php

use TalkTalk\Model\User;
use Seyon\PHPBB3\UserBundle\Security\Encoder;

$hooks['auth.user.check-signin-credentials'] = function ($submittedUserData, User $dbUser) use ($app) {

    $app['logger']->addDebug('auth.user.check-signin-credentials hook of phpbb.');
    if ('phpbb-import' !== $dbUser->provider) {
        // This user is handled by another "user provider"; let's stop here...
        return false;
    }

    $submittedPassword = $submittedUserData['password'];
    $dbUserPassword = $dbUser->password;

    $phpBbPasswordEncoder = new Encoder();

    return $phpBbPasswordEncoder->isPasswordValid($dbUserPassword, $submittedPassword);
    /*
    switch (true) {

        case (strpos($dbUserPassword, '$2a$') === 0 || strpos($dbUserPassword, '$2y$') === 0):
            // @see https://github.com/phpbb/phpbb/blob/develop-ascraeus/phpBB/phpbb/passwords/driver/bcrypt.php
            // @see https://github.com/phpbb/phpbb/blob/develop-ascraeus/phpBB/phpbb/passwords/driver/bcrypt_2y.php
            return $app['crypto.password.verify']($submittedPassword, $dbUserPassword);

        case (strpos($dbUserPassword, '$H$') === 0):
            // @see https://github.com/phpbb/phpbb/blob/develop-ascraeus/phpBB/phpbb/passwords/driver/salted_md5.php
            $dbPassword = substr($dbUserPassword, 3);
            $app['logger']->addDebug(md5($submittedPassword).' === '.$dbPassword.' ? -> '.(md5($submittedPassword) === $dbPassword));

            return md5($submittedPassword) === $dbPassword;

    }
    */
};
