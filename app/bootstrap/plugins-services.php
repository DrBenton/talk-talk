<?php

use TalkTalk\Core\Plugins\PluginsFinder;
use TalkTalk\Core\Plugins\Manager\PluginsManager;

call_user_func(
    function () use ($app) {

        $app['plugins.config_files_pattern'] = '/*/plugin-config.yml.php';

        $app['plugins.manager'] = $app->share(
            function ($app) {
                $pluginsManager =  new PluginsManager();
                $pluginsManager->setApplication($app);

                return $pluginsManager;
            }
        );

        $app['plugins.finder'] = $app->share(
            function ($app) {
                return new PluginsFinder($app['plugins.manager']);
            }
        );

    }
);
