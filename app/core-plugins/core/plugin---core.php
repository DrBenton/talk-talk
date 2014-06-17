<?php

// My Plugin PHP classes path is added to the Application autoloader
$app->get('autoloader')->addPsr4(
    'TalkTalk\\CorePlugin\\Core\\', __DIR__ . '/php/Core'
);

return new \TalkTalk\CorePlugin\Core\CorePlugin();