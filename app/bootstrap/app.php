<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

return call_user_func(
    function () {

        // General system setup
        date_default_timezone_set('UTC');

        // Silex application creation!
        $app = new Application();

        // Core params
        $app['app.path'] = realpath(__DIR__ . '/../../');
        $app['app.var.path'] = $app['app.path'] . '/app/var';
        $app['app.var.cache.path'] = $app['app.var.path'] . '/cache';
        $app['app.var.logs.path'] = $app['app.var.path'] . '/logs';
        //TODO: handle this! Unfortunately, we cannot retrieve this from our Request,
        //since we need it very soon for out plugins init ops 
        $app['app.base_url'] = '';
        $app['debug'] = true;

        // Composer autoloader is a central part of our App
        $app['autoloader'] = $app->share(function () use ($app) {
            return include $app['app.path'] . '/vendor/php/autoload.php';
        });

        // Plugins services must be initialized quickly, as all our app will rely on it :-)
        require_once __DIR__ . '/plugins-services.php';

        // Plugins init!
        require_once __DIR__ . '/plugins-init.php';

        // Some additional core logic
        $app->before(function (Request $request) use ($app) {
           //$app['app.base_url'] = $request->getBasePath();
           $app['isAjax'] = $request->isXmlHttpRequest();
        }, Application::EARLY_EVENT);

        return $app;
    }
);
