<?php

use QueryPath\DOMQuery;

$hooks['html.new_post_form'] = function (DOMQuery $html) use ($app) {
    $wysiwygInput = $html->find('#new-post-form #new-post-input-content');
    $component = 'app-modules/forum-base/components/ui/post-content-editor';
    $app['html-components.add_component']($wysiwygInput, $component);
};
