<?php

$hooks['html.header'] = function (\QueryPath\DOMQuery $html) use ($app) {
    // Some vars setup...
    $urlGenerator = $app['url_generator'];
    $translationKeyBase = 'core-plugins.auth.header_links.';
    // Ok, let's define our 3 header links!
    $headerLinks = array(
        array(
            'url' => $urlGenerator->generate('auth/sign-up', array()),
            'label' => $translationKeyBase . 'sign-up',
            'options' => array(
                'onlyForAnonymous' => true,
                'class' => 'sign-up',
            )
        ),
        array(
            'url' => $urlGenerator->generate('auth/sign-in', array()),
            'label' => $translationKeyBase . 'sign-in',
            'options' => array(
                'onlyForAnonymous' => true,
                'class' => 'sign-in',
            )
        ),
        array(
            'url' => $urlGenerator->generate('auth/sign-out', array()),
            'label' => $translationKeyBase . 'sign-out',
            'options' => array(
                'onlyForAuthenticated' => true,
                'class' => 'sign-out',
            )
        ),
    );
    // At last, we can add these header links
    $addHeaderLink = $app['utils.html.add_page_header_link'];
    foreach ($headerLinks as $headerLink) {
        call_user_func_array($addHeaderLink, array_values($headerLink));
    }
};
