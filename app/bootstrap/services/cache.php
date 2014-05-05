<?php

//for dev:
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\PhpFileCache;

//for serious usages:
//TODO: handle real Cache Providers
//use Doctrine\Common\Cache\ApcCache;
//use Doctrine\Common\Cache\RedisCache;

$app['cache.file.path'] = $app->share(function ($app) {
    return $app['app.var.cache.path'] . '/cache';
});

$app['cache'] = $app->share(function ($app) {
    // Let's disable our data cache for the moment...
    return new ArrayCache();

    return new PhpFileCache($app['cache.file.path']);
});
