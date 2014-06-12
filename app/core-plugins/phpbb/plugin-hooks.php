<?php

use TalkTalk\Model\User;
use Seyon\PHPBB3\UserBundle\Security\Encoder;
use QueryPath\Query;

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

$hooks['html.page.phpbb.imports'] = function (Query $html) use ($app, $myComponentsUrl) {
    // Add the phpBB JS imports management component
    $importsContainer = $html->find('.imports-container');
    $components = array(
        $myComponentsUrl . '/ui/phpbb-imports-gui',
        $myComponentsUrl . '/data/phpbb-imports-handler',
    );
    $app->exec('html-components.add_component', $importsContainer, implode(',', $components));
};
