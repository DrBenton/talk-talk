<?php

use TalkTalk\Core\Services\AutoloaderProxy;

return $app->servicesManager->registerServiceClass(
    'TalkTalk\Core\Services\AutoloaderProxy',
    function (AutoloaderProxy $serviceInstance) use ($app) {
        $composerLoader = include $app->vars['app.path'] . '/vendor/php/autoload.php';
        $serviceInstance->setProxyTarget($composerLoader);
    }
);