<?php

use QueryPath\DOMQuery;

// We have to use <![CDATA[ ... ]]> because of QueryPath rather strict HTML rules...
// :-/
$LIVERELOAD_SCRIPT_TAG = <<<'END'
<script><![CDATA[document.write('<script src="http://'
    + (location.host || 'localhost').split(':')[0]
    + ':%livereload-port%/livereload.js"></'
    + 'script>')]]></script>
END;

$hooks['html.site_container'] = function (DOMQuery $html) use ($app, $LIVERELOAD_SCRIPT_TAG) {
    if (!$app->vars['config']['debug']['livereload']) {
        return;
    }

    $port = (int) $app->vars['config']['debug']['livereload.port'];
    $html->find('body')->append(
        str_replace('%livereload-port%', $port, $LIVERELOAD_SCRIPT_TAG)
    );
};

$hooks['html.header'] = function (DOMQuery $html) use ($app) {
    // Add the page header links
    $headerNavList = $html->find('header nav ul');
    $headerLinks = $app->exec('utils.html.get_page_header_links');

    $headerNavList
        ->append(implode('', $headerLinks));
};

$hooks['html.alerts_container'] = function (DOMQuery $html) use ($app, $myComponentsUrl) {
    // Add the alerts JS manager component
    $alertsContainer = $html->find('#alerts-container');
    $component = $myComponentsUrl . '/ui/alerts-manager';
    $app->exec('html-components.add_component', $alertsContainer, $component);
};
