<?php

use QueryPath\DOMQuery;

$hooks['html.create_new_post_link'] = function (DOMQuery $html) use ($app, $myComponentsUrl) {
    // Add the "Ajax Posts writing" JS behaviour to the "new Post" links
    $newPostLinks = $html->find('.create-new-post-link');
//    $component = $myComponentsUrl . '/ajax-navigation';
//    $app->exec('html-components.add_component', $siteContainer, $component);
    $newPostLinks->prepend('[TODO]');
};

$hooks['html.create_new_topic_link'] = function (DOMQuery $html) use ($app, $myComponentsUrl) {
    // Add the "Ajax Posts writing" JS behaviour to the "new Topic" links
    $newTopicsLinks = $html->find('.create-new-topic-link');
    $component = $myComponentsUrl . '/ui/ajax-post-writing';
    $app->exec('html-components.add_component', $newTopicsLinks, $component);
};
