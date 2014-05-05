<?php

$hooks['html.header'] = function (\QueryPath\DOMQuery $html) use ($app) {
    // Some vars setup...
    $addHeaderLink = $app['utils.html.add_page_header_link'];
    $urlGenerator = $app['url_generator'];
    $translator = $app['translator'];
    $isAuthenticated = $app['isAuthenticated'];
    $translationKeyBase = 'core-plugins.auth.header_links.';
    // Ok, let's define our 3 header links!
    $headerLinks = array(
        array(
            'href' => $urlGenerator->generate('auth/sign-up', array()),
            'label' => $translator->trans($translationKeyBase . 'sign-up'),
            'isAjax' => true,
            'class' => 'sign-up ' . ($isAuthenticated ? 'hidden' : ''),
        ),
        array(
            'href' => $urlGenerator->generate('auth/sign-in', array()),
            'label' => $translator->trans($translationKeyBase . 'sign-in'),
            'isAjax' => true,
            'class' => 'sign-in ' . ($isAuthenticated ? 'hidden' : ''),
        ),
        array(
            'href' => $urlGenerator->generate('auth/sign-out', array()),
            'label' => $translator->trans($translationKeyBase . 'sign-out'),
            'isAjax' => true,
            'class' => 'sign-out ' . ($isAuthenticated ? '' : 'hidden'),
        ),
    );
    // At last, we can add these header links
    foreach ($headerLinks as $headerLink) {
        call_user_func_array($addHeaderLink, array_values($headerLink));
    }
};
