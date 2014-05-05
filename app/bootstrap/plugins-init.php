<?php

use TalkTalk\Core\Plugins\Manager\Behaviour\ActionsManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\ClassesManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\EventsManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\ServicesManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\LocalesManager;

call_user_func(
    function () use ($app) {

        // Core plugins discovery
        $corePluginsPath = $app['app.path'] . '/app/core-plugins';
        $app['plugins.finder']->findPlugins($corePluginsPath, $app['plugins.config_files_pattern']);

        // Third-party plugins discovery
        $thirdPartyPluginsPath = $app['app.path'] . '/plugins';
        $app['plugins.finder']->findPlugins($thirdPartyPluginsPath, $app['plugins.config_files_pattern']);

        // Plugins classes loading init
        $app['plugins.manager']->addBehaviour(new ClassesManager());
        $app['plugins.manager']->registerClassLoadingSchemes();

        // Plugins services init
        $app['plugins.manager']->addBehaviour(new ServicesManager());
        $app['plugins.manager']->registerPluginsServices();

        // Plugins actions init (map URLs to functions)
        $app['plugins.manager']->addBehaviour(new ActionsManager());
        $app['plugins.manager']->registerActions();

        // Plugins events init
        $app['plugins.manager']->addBehaviour(new EventsManager());
        $app['plugins.manager']->registerPluginsEvents();

        // Plugins own locales files init
        $app['plugins.manager']->addBehaviour(new LocalesManager($app['translator']));
        $app['plugins.manager']->registerPluginsLocales();

    }
);
