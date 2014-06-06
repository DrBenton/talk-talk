<?php

$headerLinks = array();

$app->defineFunction(
    'utils.html.add_page_header_link',
    function ($url, $labelLocaleKey, array $options = array()) use ($app, &$headerLinks) {
        $options = array_merge(
            array(
                'ajaxLink' => true,
                'onlyForAuthenticated' => false,
                'onlyForAnonymous' => false,
            ),
            $options
        );
        $headerLinks[] = $app->get('view')->getRendering(
            'utils::common/header-link',
            array('url' => $url, 'label' => $labelLocaleKey, 'options' => $options)
        );
    }
);

$app->defineFunction(
    'utils.html.get_page_header_links',
    function () use (&$headerLinks) {
        return $headerLinks;
    }
);

$app->vars['utils.html.breadcrumb.home'] = array(
    'url' => $app->path('core/home'),
    'label' => 'core-plugins.utils.breadcrumb.home',
    'class' => 'home',
);
