<?php

use QueryPath\DOMQuery;

$hooks['html.site_container'] = function (DOMQuery $html) use ($app)  {
    // Add the "Ajax links handler" JS behaviour to #site-container
    $siteContainer = $html->find('#site-container');
    $component = 'app-modules/ajax-nav/components/ajax-navigation';
    $app['html-components.add_component']($siteContainer, $component);
};

$hooks['html.perfs_info'] = function (DOMQuery $html) use ($app)  {
    $debugInfo = $html->find('#perfs-info');

    // 1) "Advanced perfs" debug info Component
    $component = 'app-modules/ajax-nav/components/ui/debug/ajax-advanced-perfs-debug-info';
    $app['html-components.add_component']($debugInfo, $component);

    // 2) "Ajax loadings" debug info Component
    $ajaxDebugInfoHtml = $app['twig']->render('ajax-navigation/debug/ajax-debug-info.twig');
    $debugInfo->prepend($ajaxDebugInfoHtml);
    $ajaxLoadingsDebugInfo = $html->find('#ajax-loadings-debug-info');
    $component = 'app-modules/ajax-nav/components/ui/debug/ajax-loadings-debug-info';
    $app['html-components.add_component']($ajaxLoadingsDebugInfo, $component);
};
