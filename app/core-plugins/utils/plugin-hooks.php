<?php

$hooks['html.header'] = function (\QueryPath\DOMQuery $html) use ($app) {
    // Add the page header links
    $headerNavList = $html->find('header nav ul');
    $headerLinks = $app['utils.html.get_page_header_links']();

    $headerNavList
        ->append(implode('', $headerLinks));
};
