<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

return function (Request $request) {

    // General system setup
    date_default_timezone_set('UTC');

    // Silex application creation!
    $app = new Application();

    // Core params
    //TODO: handle this in a config file
    $app['app.path'] = realpath(__DIR__ . '/../../');
    $app['app.var.path'] = $app['app.path'] . '/app/var';
    $app['app.var.cache.path'] = $app['app.var.path'] . '/cache';
    $app['app.var.logs.path'] = $app['app.var.path'] . '/logs';
    $app['app.vendors.path'] = $app['app.path'] . '/vendor';
    $app['app.vendors.php.path'] = $app['app.vendors.path'] . '/php';
    $app['app.vendors.js.path'] = $app['app.vendors.path'] . '/js';
    $app['debug'] = true;

    // Composer autoloader is a central part of our App.
    // Let's add it as a shared service!
    $app['autoloader'] = $app->share(
        function () use ($app) {
            return include $app['app.path'] . '/vendor/php/autoload.php';
        }
    );

    // We need a Request right now, as some plugins logic may need our app base path
    $app['app.base_url'] = $request->getBasePath();
    $app['isAjax'] = $request->isXmlHttpRequest();

    // Plugins services must be initialized quickly, as all our app will rely on it :-)
    require_once __DIR__ . '/services/services.php';

    // Cache service may be useful ASAP too for early data cache
    require_once __DIR__ . '/services/cache.php';

    // Plugins init!
    require_once __DIR__ . '/plugins-init.php';

    // Some additional core logic
    $app->before(
        function (Request $request) use ($app) {
        },
        Application::EARLY_EVENT
    );

    return $app;
};
