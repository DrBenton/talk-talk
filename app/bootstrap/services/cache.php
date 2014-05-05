<?php

//for dev:
use Doctrine\Common\Cache\PhpFileCache;
//for serious usages:
//use Doctrine\Common\Cache\ApcCache;
//use Doctrine\Common\Cache\RedisCache;

call_user_func(
    function () use ($app) {

        $app['cache.file.path'] = $app->share(function ($app) {
          return $app['app.var.cache.path'] . '/cache';
        });

        $app['cache'] = $app->share(function ($app) {
          return new PhpFileCache($app['cache.file.path']);
        });

    }
);
