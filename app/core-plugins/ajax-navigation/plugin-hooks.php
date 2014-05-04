<?php

$hooks['html.site_container'] = function (\QueryPath\DOMQuery $html) {
    // Add the "Ajax links handler" JS behaviour to #site-container
    $html->find('#site-container')
        ->addClass('requirejs-widget')
        ->attr('data-widget', 'app-modules/ajax-nav/widgets/ajax-links-handler');
};
