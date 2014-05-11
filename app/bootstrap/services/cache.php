<?php

//for dev:
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\PhpFileCache;

//for serious usages:
//TODO: handle real Cache Providers
//use Doctrine\Common\Cache\ApcCache;
//use Doctrine\Common\Cache\RedisCache;

$app['cache.prefix'] = 'talk-talk';

$app['cache.file.path'] = $app->share(
    function ($app) {
        return $app['app.var.cache.path'] . '/data-cache';
    }
);

$app['cache'] = $app->share(
    function ($app) {

        if (
            isset($app['config']['data-cache']['enabled']) &&
            false == $app['config']['data-cache']['enabled']
        ) {
            // The app data cache is disabled: let's use the ArrayCache!
            return new ArrayCache();
        }

        //TODO: allow full data cache customization through the app "main.ini.php" file
        return new PhpFileCache($app['cache.file.path']);
    }
);
