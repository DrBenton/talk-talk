<?php

use TalkTalk\Core\Plugins\Manager\Behaviour\ActionsVariablesConvertersManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\ActionsManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\ClassesManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\EventsManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\ServicesManager;
use TalkTalk\Core\Plugins\Manager\Behaviour\LocalesManager;

call_user_func(
    function () use ($app) {

        // Core plugins discovery
        $corePluginsPath = $app->vars['app.path'] . '/app/core-plugins';
        $app->pluginsFinder->findPlugins($corePluginsPath, $app->vars['plugins.config_files_pattern']);

        // Third-party plugins discovery
        $thirdPartyPluginsPath = $app->vars['app.path'] . '/plugins';
        $app->pluginsFinder->findPlugins($thirdPartyPluginsPath, $app->vars['plugins.config_files_pattern']);

        // Plugins classes loading init
        $app->pluginsManager->addBehaviour(new ClassesManager());
        $app->pluginsManager->registerClassLoadingSchemes();

        // Plugins services init
        $app->pluginsManager->addBehaviour(new ServicesManager());
        $app->pluginsManager->registerPluginsServices();

        // Plugins actions variables converters init (map URLs params to objects)
        $app->pluginsManager->addBehaviour(new ActionsVariablesConvertersManager());
        $app->pluginsManager->registerPluginsActionsVariablesConverters();

        // Plugins actions init (map URLs to functions)
        $app->pluginsManager->addBehaviour(new ActionsManager());
        $app->pluginsManager->registerActions();

        // Plugins events init
        $app->pluginsManager->addBehaviour(new EventsManager());
        $app->pluginsManager->registerPluginsEvents();

        // Plugins own locales files init
        $app->pluginsManager->addBehaviour(new LocalesManager($app->vars['translator']));
        $app->pluginsManager->registerPluginsLocales();

    }
);
