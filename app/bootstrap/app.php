<?php

$appPackedFilePath = __DIR__ . '/../var/cache/php-packs/app.pack.php';
if (file_exists($appPackedFilePath)) {
    require_once $appPackedFilePath;
}

use TalkTalk\Core\Application;
use TalkTalk\Core\ServicesManager;

return function () {

    // General system setup
    date_default_timezone_set('UTC');

    // Silex application creation!
    $app = new Application();

    // Core params
    //TODO: handle this in a config file?
    $app->vars['app.path'] = realpath(__DIR__ . '/../../');
    $app->vars['app.var.path'] = $app->vars['app.path'] . '/app/var';
    $app->vars['app.var.cache.path'] = $app->vars['app.var.path'] . '/cache';
    $app->vars['app.var.logs.path'] = $app->vars['app.var.path'] . '/logs';
    $app->vars['app.vendors.path'] = $app->vars['app.path'] . '/vendor';
    $app->vars['app.vendors.php.path'] = $app->vars['app.vendors.path'] . '/php';
    $app->vars['app.vendors.js.path'] = $app->vars['app.vendors.path'] . '/js';
    $app->vars['app.error'] = null;
    $app->vars['app.http_status_code'] = 200;//until now, everything is fine :-)
    $app->vars['perfs.start-time'] = microtime(true);

    $app->vars['app.base_url'] = $app->request->getResourceUri();
    $app->vars['isAjax'] = $app->request->isAjax();

    // ServicesManager init: this is our entry point for other Services!
    $app->container->singleton(
        'servicesManager',
        function () use ($app) {
            $servicesManager = new ServicesManager();
            $servicesManager->setApplication($app);

            return $servicesManager;
        }
    );

    // Let's load the very heart of our app config...
    $mainConfigFilePath = $app->vars['app.path'] . '/app/config/main.ini.php';
    $app->vars['config'] = parse_ini_file($mainConfigFilePath, true);

    // ...and start using it right now!
    $app->config('debug', (bool) $app->vars['config']['debug']['debug']);

    // We have a few bootstrap Services to register very early.
    // Let's create a simple Closure to handle them like the PluginsManager
    $loadBootstrapService = function ($serviceName) use ($app) {
        // Services PHP code only have access to our $app
        require_once __DIR__ . '/bootstrap-services-init/' . $serviceName . '.php';
    };

    // Composer autoloader is a central part of our App.
    $loadBootstrapService('autoloader');

    // Plugins services must be initialized quickly, as all our app will rely on it :-)
    $loadBootstrapService('plugins-finder');
    $loadBootstrapService('plugins-manager');

    // Cache service may be useful ASAP too for early data cache
    $loadBootstrapService('cache');

    // Some performances-related stats
    if ($app->vars['config']['debug']['perfs.tracking.enabled']) {
        $app->vars['perfs.bootstrap.time_elapsed'] = round(microtime(true) - $app->vars['perfs.start-time'], 3);
        $app->vars['perfs.bootstrap.nb_included_files'] = count(get_included_files());
    }

    $app->get('/', function() use ($app) {
       echo '<pre>' . print_r(get_included_files(), true) . '</pre>';
    });

    // Plugins init!
    require_once __DIR__ . '/plugins-init.php';

    // Session start
    session_cache_limiter(false);
    session_start();

    if ($app->vars['config']['debug']['perfs.tracking.enabled']) {
        $app->vars['perfs.plugins-init.time_elapsed'] = round(microtime(true) - $app->vars['perfs.start-time'], 3);
        $app->vars['perfs.plugins-init.nb_included_files'] = count(get_included_files());
    }



    return $app;
};
