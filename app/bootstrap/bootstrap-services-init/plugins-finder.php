<?php

use TalkTalk\Core\Services\PluginsFinder;

$app->vars['plugins.config_files_pattern'] = '/*/plugin-config.yml.php';

return $app->servicesManager->registerServiceClass(
    'TalkTalk\Core\Services\PluginsFinder',
    function (PluginsFinder $serviceInstance) use ($app) {
        $serviceInstance->setCache($app->cache);
    }
);




