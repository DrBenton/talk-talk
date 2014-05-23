<?php

use TalkTalk\CorePlugins\Core\SettingsManager;

$app['settings'] = $app->share(
    function () use ($app) {
        return new SettingsManager();
    }
);
