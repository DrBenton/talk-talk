<?php

// My Plugin PHP classes path is added to the Application autoloader
$app->get('autoloader')->addPsr4(
    'TalkTalk\\CorePlugin\\Utils\\', __DIR__ . '/php/Utils'
);

return new \TalkTalk\CorePlugin\Utils\UtilsPlugin();