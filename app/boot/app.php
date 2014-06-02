<?php

return function() {

    // Core vars setup
    $bootPath = __DIR__;
    $appPath = dirname($bootPath);
    $rootPath = dirname($appPath);
    $appVarPath = $appPath . '/var';
    $appCachePath = $appVarPath . '/cache';
    $appPhpPacksPath = $appCachePath . '/php-packs';
    $appVendorsPath = $rootPath . '/vendor/php';

    // Do we have some PHP packs to load early?
    $appPhpPackFilePath = $appPhpPacksPath . '/app.pack.php';
    if (file_exists($appPhpPackFilePath)) {
        include_once $appPhpPackFilePath;
    }
    $vendorsPhpPackFilePath = $appPhpPacksPath . '/vendors.pack.php';
    if (file_exists($vendorsPhpPackFilePath)) {
        include_once $vendorsPhpPackFilePath;
    }

    // Composer loading
    require_once $appVendorsPath . '/autoload.php';

    // Okay, let's create our Application!
    $slimApp = new \Slim\Slim();
    $slimApp->config('debug', true);
    $app = new \TalkTalk\Core\Application($slimApp);

    // App core vars definition
    $app->vars['app.root_path'] = $rootPath;
    $app->vars['app.app_path'] = $appPath;
    $app->vars['app.boot_path'] = $bootPath;
    $app->vars['app.var_path'] = $appVarPath;
    $app->vars['app.cache_path'] = $appCachePath;
    $app->vars['app.php_packs_path'] = $appPhpPacksPath;
    $app->vars['app.php_vendors_path'] = $appVendorsPath;
    $app->vars['app.boot_services_path'] = $app->vars['app.boot_path'] . '/core-services-init';

    $app->vars['request'] = $slimApp->request;

    // Core Services init:
    // Packing Manager
    $app->includeInApp($app->vars['app.boot_services_path'] . '/packing-manager.php');

    // Plugins system init
    include_once __DIR__ . '/plugins-system-init.php';


    return $app;

};