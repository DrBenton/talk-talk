<?php

$action = function () use ($app) {

    $viewData = array(
        'itemsTypes' => array(
            'users' => 'Users',
            'forums' => 'Forums',
            'topics' => 'Topics',
            'posts' => 'Posts',
        )
    );

    return $app->get('view')->render(
        'phpbb::import/importing-page',
        $viewData
    );
};

return $action;