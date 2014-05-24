<?php

use QueryPath\DOMQuery;

$hooks['html.site_container'] = function (DOMQuery $html) use ($app)  {
    // Add the "Ajax links handler" JS behaviour to #site-container
    $siteContainer = $html->find('#site-container');
    $components = array(
        'app-modules/ajax-nav/components/data/ajax-links-handler',
        'app-modules/ajax-nav/components/data/ajax-content-loader',
        'app-modules/ajax-nav/components/data/ajax-history',
    );
    $app['html-components.add_component']($siteContainer, $components);
};
