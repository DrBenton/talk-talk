<?php

use TalkTalk\Core\Service\PluginsFinder;

$app->vars['plugins.config_files_pattern'] = '/*/plugin-config.yml.php';

$app->defineService(
    'plugins.finder',
    function () use ($app) {
        $service = new PluginsFinder();
        $service->setPluginsConfigFilesGlobPattern($app->vars['plugins.config_files_pattern']);

        return $service;
    }
);
