<?php

use QueryPath\DOMQuery;

// Page header stuff

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

$app->defineFunction(
    'utils.html.escape',
    function ($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
);

// Page breadcrumb stuff

$app->defineFunction(
    'utils.html.breadcrumb.get_home_part',
    function () use ($app) {
        return array(
            'url' => $app->path('core/home'),
            'label' => 'core-plugins.utils.breadcrumb.home',
            'class' => 'home',
        );
    }
);

// HTML components stuff - powered by Twitter Flight

$app->defineFunction(
    'html-components.add_component',
    function (DOMQuery $node, $componentsNames) use ($app) {
        $nodeCurrentComponents = explode(',', $node->attr('data-component'));
        $componentsNames = $app->get('utils.array')->getArray($componentsNames);
        $nodeCurrentComponents = array_merge($nodeCurrentComponents, $componentsNames);
        $nodeCurrentComponents = array_filter($nodeCurrentComponents, function ($componentName) {
            return is_string($componentName) && strlen($componentName) > 0;
        });
        $node->addClass('flight-component');
        $node->attr('data-component', implode(',', $nodeCurrentComponents));
    }
);
