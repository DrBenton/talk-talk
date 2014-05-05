<?php

use TalkTalk\Core\Plugins\Manager\PluginsManager;
use TalkTalk\Core\Plugins\PluginsFinder;

$app['plugins.config_files_pattern'] = '/*/plugin-config.yml.php';

$app['plugins.manager'] = $app->share(
    function ($app) {
        $pluginsManager = new PluginsManager();
        $pluginsManager->setApplication($app);
        $pluginsManager->setLogger($app['logger']);
        $pluginsManager->setCache($app['cache']);

        return $pluginsManager;
    }
);

$app['plugins.finder'] = $app->share(
    function ($app) {
        $pluginsFinder = new PluginsFinder($app['plugins.manager']);
        $pluginsFinder->setCache($app['cache']);

        return $pluginsFinder;
    }
);
