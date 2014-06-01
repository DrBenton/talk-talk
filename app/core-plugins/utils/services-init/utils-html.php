<?php

$headerLinks = array();

$app['utils.html.add_page_header_link'] = $app->protect(
    function ($url, $labelLocaleKey, array $options = array()) use ($app, &$headerLinks) {
        $options = array_merge(
            array(
                'ajaxLink' => true,
                'onlyForAuthenticated' => false,
                'onlyForAnonymous' => false,
            ),
            $options
        );
        $headerLinks[] = $app['twig']->render(
            'utils/common/header-link.twig',
            array('url' => $url, 'label' => $labelLocaleKey, 'options' => $options)
        );
    }
);

$app['utils.html.get_page_header_links'] = $app->protect(
    function () use (&$headerLinks) {
        return $headerLinks;
    }
);

$app['utils.html.breadcrumb.home'] = function () use ($app) {
    return array(
        'url' => $app['url_generator']->generate('core/home'),
        'label' => 'core-plugins.utils.breadcrumb.home',
        'class' => 'home',
    );
};
