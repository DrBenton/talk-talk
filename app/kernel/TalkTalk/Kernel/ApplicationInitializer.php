<?php

namespace TalkTalk\Kernel;


class ApplicationInitializer
{

    public static function initCoreServices(ApplicationInterface $app)
    {
        // Core Services
        $app->defineService('autoloader', function() use ($app) {
            $composerAutoloader = include $app->vars['app.php_vendors_path'] . '/autoload.php';

            return $composerAutoloader;
        });
        $app->defineService('logger', function() use ($app) {
            $service = new Service\Logger($app->vars['app.var_path'] . '/logs');

            return $service;
        });
        $app->defineService('hooks', function() use ($app) {
            $service = new Service\Hooks();

            return $service;
        });
        $app->defineService('template-renderer', function() use ($app) {
            $service = new Service\TemplateRenderer();

            return $service;
        });

        // Plugins-related Services
        $app->defineService('plugins.finder', function() use ($app) {
            $service = new Service\PluginsFinder();
            $service->setPluginsFilesGlobPattern('*/plugin---*.php');
            $service->setThemesFilesGlobPattern('*/theme---*.php');

            return $service;
        });
        $app->defineService('plugins.initializer', function() use ($app) {
            $service = new Service\PluginsInitializer();

            return $service;
        });

        // "Utils" Services
        $app->defineService('utils.io', function() use ($app) {
            $service = new Service\IOUtils();

            return $service;
        });
        $app->defineService('utils.array', function() use ($app) {
            $service = new Service\ArrayUtils();

            return $service;
        });
        $app->defineService('utils.string', function() use ($app) {
            $service = new Service\StringUtils();

            return $service;
        });
    }

    public static function initPlugins(ApplicationInterface $app)
    {
        $pluginsFinder = $app->get('plugins.finder');
        // Core plugins search
        $pluginsFinder->findPlugins($app->vars['app.app_path'] . '/core-plugins');
        // Themes search
        $pluginsFinder->findThemes($app->vars['app.root_path'] . '/themes');

        // Plugins init!
        $app->get('plugins.initializer')->initPlugins($pluginsFinder->getPlugins());
    }

}