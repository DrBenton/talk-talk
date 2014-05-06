<?php

$LIVERELOAD_SCRIPT_TAG = <<<'END'
<script><![CDATA[document.write('<script src="http://'
    + (location.host || 'localhost').split(':')[0]
    + ':${livereload_port}/livereload.js"></'
    + 'script>')]]></script>
END;

$hooks['html.site_container'] = function (\QueryPath\DOMQuery $html) use ($app, $LIVERELOAD_SCRIPT_TAG) {
    if (!$app['debug']) {
        return;
    }

    $port = (int) $app['config']['development']['livereload.port'];
    $html->find('body')->append(
        str_replace('${livereload_port}', $port, $LIVERELOAD_SCRIPT_TAG)
    );
};

$hooks['html.header'] = function (\QueryPath\DOMQuery $html) use ($app) {
    // Add the page header links
    $headerNavList = $html->find('header nav ul');
    $headerLinks = $app['utils.html.get_page_header_links']();

    $headerNavList
        ->append(implode('', $headerLinks));
};
