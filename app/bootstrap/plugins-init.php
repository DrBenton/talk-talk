<?php

use Silex\Application;
use TalkTalk\Core\Plugins\Manager\Behaviour\ServicesManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\ActionsManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\ClassesManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\TwigViewsFinder;
use TalkTalk\Core\Plugins\Manager\Behaviour\TwigExtensionsManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\AssetsManager;

call_user_func(
    function () use ($app) {

        // Plugins manager init
        $app['plugins.manager']->addBehaviour(new ServicesManager());
        $app['plugins.manager']->addBehaviour(new ActionsManager());
        $app['plugins.manager']->addBehaviour(new ClassesManager());

        // Core plugins discovery
        $corePluginsPath = $app['app.path'] . '/app/core-plugins';
        $app['plugins.finder']->findPlugins($corePluginsPath, $app['plugins.config_files_pattern']);

        // Third-party plugins discovery
        $thirdPartyPluginsPath = $app['app.path'] . '/plugins';
        $app['plugins.finder']->findPlugins($thirdPartyPluginsPath, $app['plugins.config_files_pattern']);

        // Plugins classes loading init
        $app['plugins.manager']->registerClassLoadingSchemes();

        // Plugins services init
        $app['plugins.manager']->registerPluginsServices();
        
        // Plugins actions init (map URLs to functions)
        $app['plugins.manager']->registerActions();

        // Some plugins ops can be resolved later
        $app->before(function () use ($app) {


        }, Application::EARLY_EVENT - 1);

    }
);
