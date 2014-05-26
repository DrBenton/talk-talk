<?php

use QueryPath\DOMQuery;

$hooks['html.site_container'] = function (DOMQuery $html) use ($app)  {
    // Add the "Ajax links handler" JS behaviour to #site-container
    $siteContainer = $html->find('#site-container');
    $component = 'app-modules/ajax-nav/components/ajax-navigation';
    $app['html-components.add_component']($siteContainer, $component);
};
