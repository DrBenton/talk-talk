<?php

use TalkTalk\Core\Plugins\Manager\PluginsManager;
use TalkTalk\Core\Plugins\PluginsFinder;

$app->vars['plugins.config_files_pattern'] = '/*/plugin-config.yml.php';

// Plugins manager init
$app->container->singleton(
    'pluginsManager',
    function ($c) use ($app) {
        $pluginsManager = new PluginsManager();
        $pluginsManager->setApplication($app);
        $pluginsManager->setLogger($app->log);
        $pluginsManager->setCache($app->cache);

        return $pluginsManager;
    }
);

// Plugins finder init
$app->container->singleton(
    'pluginsFinder',
    function ($c) use ($app) {
        $pluginsFinder = new PluginsFinder($app->pluginsManager);
        //$pluginsFinder->setLogger($app->log);
        $pluginsFinder->setCache($app->cache);

        return $pluginsFinder;
    }
);


