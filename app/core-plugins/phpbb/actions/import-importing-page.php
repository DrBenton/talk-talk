<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\CorePlugins\PhpBb\Model\User as PhpBbUser;

$action = function (Application $app, Request $request) {
    
    $viewData = array(
        'phpBb' => array(
            'nbUsers' => PhpBbUser::whereIn(
                'user_type', array(PhpBbUser::TYPE_USER, PhpBbUser::TYPE_ADMIN)
            )->count()
        )
    );
    
    return $app['twig']->render(
        'phpbb/importing/importing-page.twig',
        $viewData
    );
};

return $action;
