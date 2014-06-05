<?php

use TalkTalk\Model\User;

$hooks['auth.user.check-signin-credentials'] = function (array $submittedUserData, User $dbUser) use ($app) {

    $app->get('logger')
        ->debug('auth.user.check-signin-credentials hook of "auth" Plugin.');

    if ('talk-talk' !== $dbUser->provider) {
        // This user is handled by another "user provider"; let's stop here...
        return null;
    }

    return $app
        ->get('crypto')
        ->verifyPassword($submittedUserData['password'], $dbUser->password);
};

$hooks['html.header'] = function () use ($app) {
    // Some vars setup...
    $translationKeyBase = 'core-plugins.auth.header_links.';
    // Ok, let's define our 3 header links!
    $headerLinks = array(
        array(
            'url' => $app->path('auth/sign-up', array()),
            'label' => $translationKeyBase . 'sign-up',
            'options' => array(
                'onlyForAnonymous' => true,
                'class' => 'sign-up',
            )
        ),
        array(
            'url' => $app->path('auth/sign-in', array()),
            'label' => $translationKeyBase . 'sign-in',
            'options' => array(
                'onlyForAnonymous' => true,
                'class' => 'sign-in',
            )
        ),
        array(
            'url' => $app->path('auth/sign-out', array()),
            'label' => $translationKeyBase . 'sign-out',
            'options' => array(
                'onlyForAuthenticated' => true,
                'class' => 'sign-out',
            )
        ),
    );
    // At last, we can add these header links
    $appPageHeaderLink = $app->getFunction('utils.html.add_page_header_link');
    foreach ($headerLinks as $headerLink) {
        call_user_func_array($appPageHeaderLink, array_values($headerLink));
    }
};