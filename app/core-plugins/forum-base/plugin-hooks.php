<?php

use QueryPath\DOMQuery;

$hooks['html.new_post_form'] = function (DOMQuery $html) use ($app) {
    $html->find('#new-post-form #new-post-input-content')
        ->addClass('requirejs-widget')
        ->attr('data-widget', 'app-modules/forum-base/widgets/post-content-editor');
};
