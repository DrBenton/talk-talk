<?php

use QueryPath\DOMQuery;

$hooks['html.component.post_content_editor'] = function (DOMQuery $html) use ($app, $myComponentsUrl) {
    $wysiwygInput = $html->find('form.post-content-form textarea.content');
    $app->exec(
        'html-components.add_component',
        $wysiwygInput,
        $myComponentsUrl . '/ui/post-content-editor'
    );
};
