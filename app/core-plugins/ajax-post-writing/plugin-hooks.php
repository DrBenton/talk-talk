<?php

use QueryPath\DOMQuery;

$hooks['html.create_new_post_link'] = function (DOMQuery $html) use ($app, $myComponentsUrl) {
    // Add the "Ajax Posts writing" JS behaviour to the "new Post" links
    $newPostLinks = $html->find('.create-new-post-link');
    $component = $myComponentsUrl . '/ui/ajax-post-writing-new-content-buttons-handler';
    $app->exec('html-components.add_component', $newPostLinks, $component);
};

$hooks['html.create_new_topic_link'] = function (DOMQuery $html) use ($app, $myComponentsUrl) {
    // Add the "Ajax Posts writing" JS behaviour to the "new Topic" links
    $newTopicsLinks = $html->find('.create-new-topic-link');
    $component = $myComponentsUrl . '/ui/ajax-post-writing-new-content-buttons-handler';
    $app->exec('html-components.add_component', $newTopicsLinks, $component);
};
