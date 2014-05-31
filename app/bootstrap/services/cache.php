<?php

//for dev:
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\PhpFileCache;

//for serious usages:
//TODO: handle real Cache Providers
//use Doctrine\Common\Cache\ApcCache;
//use Doctrine\Common\Cache\RedisCache;

$app->vars['cache.prefix'] = 'talk-talk';

$app->vars['cache.file.path'] = $app->vars['app.var.cache.path'] . '/data-cache';

$app->container->singleton(
    'cache',
    function ($c) use ($app) {
        if (
            isset($app->vars['config']['data-cache']['enabled']) &&
            false == $app->vars['config']['data-cache']['enabled']
        ) {
            // The app data cache is disabled: let's use the ArrayCache!
            return new ArrayCache();
        }

        //TODO: allow full data cache customization through the app "main.ini.php" file
        return new PhpFileCache($app->vars['cache.file.path']);
    }
);
