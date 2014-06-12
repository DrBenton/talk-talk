<?php

//for dev:
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\PhpFileCache;

//for serious usages:
//TODO: handle real Cache Providers
//use Doctrine\Common\Cache\ApcCache;
//use Doctrine\Common\Cache\RedisCache;

$app->vars['cache.prefix'] = 'talk-talk';

$app->defineFunction(
    'cache.get_path',
    function () use ($app) {
        return $app->vars['app.cache_path'] . '/data-cache';
    }
);

$app->defineService(
    'cache',
    function () use ($app) {

        if (empty($app->vars['config']['data-cache']['enabled'])) {
            // The app data cache is disabled: let's use the ArrayCache!
            return new ArrayCache();
        }

        //TODO: allow full data cache customization through the app "main.ini.php" file
        return new PhpFileCache($app->exec('cache.get_path'));
    }
);
