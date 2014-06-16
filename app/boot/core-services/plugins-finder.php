<?php

use TalkTalk\Core\Service\PluginsFinder;

$app->vars['plugins.plugins_files_pattern'] = '/*/plugin---*.php';

$app->defineService(
    'plugins.finder',
    function () use ($app) {
        $service = new PluginsFinder();
        $service->setPluginsFilesGlobPattern($app->vars['plugins.plugins_files_pattern']);

        return $service;
    }
);
