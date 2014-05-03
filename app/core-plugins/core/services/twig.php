<?php

use TalkTalk\CorePlugins\Core\PluginsManagerBehaviour\AssetsManager;
use TalkTalk\CorePlugins\Core\PluginsManagerBehaviour\TwigExtensionsManager;
use TalkTalk\CorePlugins\Core\PluginsManagerBehaviour\TwigViewsFinder;

$app['plugins.manager']->addBehaviour(new TwigViewsFinder());

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    array(
        'twig.path' => $app['plugins.manager']->getPluginsViewsPaths(),
        'twig.options' => array(
            'cache' => $app['debug'] ? null : $app['app.var.cache.path'] . '/twig',
            'strict_variables' => true,
        )
    )
);

// Plugins assets management
$app->before(
    function () use ($app) {

        // Plugins Twig Extensions registering
        $app['plugins.manager']->addBehaviour(new TwigExtensionsManager());
        $app['plugins.manager']->registerTwigExtensions();

        // Plugins assets init
        $app['plugins.manager']->addBehaviour(new AssetsManager());
        $app['plugins.manager']->registerPluginsAssets();

    }
);
