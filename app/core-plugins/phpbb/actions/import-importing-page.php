<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$action = function (Application $app, Request $request) {

    $viewData = array(
        'itemsTypes' => array(
            'users' => 'Users',
            'forums' => 'Forums',
            'topics' => 'Topics',
            'posts' => 'Posts',
        )
    );

    return $app['twig']->render(
        'phpbb/importing/importing-page.twig',
        $viewData
    );
};

return $action;
