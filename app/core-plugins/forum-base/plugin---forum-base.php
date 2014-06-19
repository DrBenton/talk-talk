<?php

// My Plugin PHP classes path is added to the Application autoloader
$app->get('autoloader')->addPsr4(
    'TalkTalk\\CorePlugin\\ForumBase\\', __DIR__ . '/php/ForumBase'
);

return new \TalkTalk\CorePlugin\ForumBase\ForumBasePlugin();