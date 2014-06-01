<?php

use TalkTalk\Core\Services\PluginsManager;

return $app->servicesManager->registerServiceClass(
    'TalkTalk\Core\Services\PluginsManager',
    function (PluginsManager $serviceInstance) use ($app) {
        $serviceInstance->setLogger($app->log);
        $serviceInstance->setCache($app->cache);
    }
);
