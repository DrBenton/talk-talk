<?php

// My Theme PHP classes path is added to the Application autoloader
$app->get('autoloader')->addPsr4(
    'TalkTalk\\Theme\\TWBootstrap\\', __DIR__ . '/php/TWBootstrap'
);


return new \TalkTalk\Theme\TWBootstrap\TWBootstrapPlugin();