<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

return function (Request $request) {

    // General system setup
    date_default_timezone_set('UTC');

    // Silex application creation!
    $app = new Application();

    // Core params
    //TODO: handle this in a config file?
    $app['app.path'] = realpath(__DIR__ . '/../../');
    $app['app.var.path'] = $app['app.path'] . '/app/var';
    $app['app.var.cache.path'] = $app['app.var.path'] . '/cache';
    $app['app.var.logs.path'] = $app['app.var.path'] . '/logs';
    $app['app.vendors.path'] = $app['app.path'] . '/vendor';
    $app['app.vendors.php.path'] = $app['app.vendors.path'] . '/php';
    $app['app.vendors.js.path'] = $app['app.vendors.path'] . '/js';
    $app['app.error'] = null;
    $app['perfs.start-time'] = microtime(true);
    // Some of our Plugins may need a "request" very soon, before the Silex "#handle()"
    // method is triggered.
    // Since PHP script always run in response to a HTTP request,
    // we can give our $app a first Request right now!
    $app['request'] = $request;

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

    // We have a few bootstrap Services to register very early.
    // Let's create a simple Closure to handle them like the PluginsManager
    $loadBootstrapService = function ($serviceName) use ($app) {
        // Services PHP code only have access to our $app
        require_once __DIR__ . '/services/' . $serviceName . '.php';
    };

    // Let's load the very heart of our app config...
    $loadBootstrapService('config');

    // ...and start using it right now!
    $app['debug'] = $app['config']['general']['debug'];
    ErrorHandler::register();
    ExceptionHandler::register($app['debug']);

    // Anybody can need a Logger; let's initialize this Service first!
    $loadBootstrapService('logger');

    // Plugins services must be initialized quickly, as all our app will rely on it :-)
    $loadBootstrapService('plugins');

    // Cache service may be useful ASAP too for early data cache
    $loadBootstrapService('cache');

    // Some performances-related stats
    $app['perfs.bootstrap.duration'] = round(microtime(true) - $app['perfs.start-time'], 3);
    $app['perfs.bootstrap.nb-included-files'] = count(get_included_files());

    // Plugins init!
    require_once __DIR__ . '/plugins-init.php';

    $app['perfs.plugins-init.duration'] = round(microtime(true) - $app['perfs.start-time'], 3);
    $app['perfs.plugins-init.nb-included-files'] = count(get_included_files());

    return $app;
};
