<?php

//for dev:
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\PhpFileCache;

//for serious usages:
//TODO: handle real Cache Providers
//use Doctrine\Common\Cache\ApcCache;
//use Doctrine\Common\Cache\RedisCache;

use TalkTalk\Core\Services\CacheProxy;

$app->vars['cache.prefix'] = 'talk-talk';

$app->vars['cache.file.path'] = $app->vars['app.var.cache.path'] . '/data-cache';

return $app->servicesManager->registerServiceClass(
    'TalkTalk\Core\Services\CacheProxy',
    function (CacheProxy $serviceInstance) use ($app) {
        if (
            isset($app->vars['config']['data-cache']['enabled']) &&
            false == $app->vars['config']['data-cache']['enabled']
        ) {
            // The app data cache is disabled: let's use the ArrayCache!
            $serviceInstance->setProxyTarget(new ArrayCache());
        } else {
            //TODO: allow full data cache customization through the app "main.ini.php" file
            $serviceInstance->setProxyTarget(
                new PhpFileCache($app->vars['cache.file.path'])
            );
        }
    }
);
