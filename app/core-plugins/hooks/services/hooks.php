<?php

use TalkTalk\CorePlugins\Hooks\PluginsManagerBehaviour\HooksManager;

$app->before(function () use ($app) {
    $app['plugins.manager']->addBehaviour(new HooksManager());
});
