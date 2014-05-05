<?php

$headerLinks = array();

$app['utils.html.add_page_header_link'] = $app->protect(
    function ($href, $label, $ajaxLink = true, $class = '') use (&$headerLinks) {
        $ajaxLinkClass = $ajaxLink ? 'ajax-link' : '';
        $headerLinks[] = <<<HTML
    <li class="$class">
        <a href="$href" class="$class $ajaxLinkClass">$label</a>
    </li>
HTML;
    }
);

$app['utils.html.get_page_header_links'] = $app->protect(
    function () use (&$headerLinks) {
        return $headerLinks;
    }
);
