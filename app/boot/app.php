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
    $startTime = microtime(true);

    // A constant for some security checks
    define('APP_ENVIRONMENT', true);

    // Do we have some PHP classes packs to load early?
    $phpClassesPacks = array(
        'app/boot/classes',
        'vendors/all',
        'vendors/slim',
    );
    foreach ($phpClassesPacks as $phpPack) {
        $phpPackFilePath = $appPhpPacksPath . '/' . $phpPack . '.pack.php';
        if (file_exists($phpPackFilePath)) {
            include_once $phpPackFilePath;
        }
    }

    // Composer loading - only if needed at this point!
    if (!class_exists('TalkTalk\Core\Application')) {
        require_once $appVendorsPath . '/autoload.php';
    }

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

    $app->vars['perfs.start_time'] = $startTime;
    $app->vars['request'] = $slimApp->request;

    // Services packs management
    $phpIncludedInAppServicesPacks = array(
        'app/boot/services',
    );
    foreach ($phpIncludedInAppServicesPacks as $phpPack) {
        $phpPackFilePath = $appPhpPacksPath . '/' . $phpPack . '.pack.php';
        if (file_exists($phpPackFilePath)) {
            $app->includeInApp($phpPackFilePath);
        }
    }

    // Core Services init:
    $app->includeInApp($app->vars['app.boot_services_path'] . '/packing-manager.php');
    $app->includeInApp($app->vars['app.boot_services_path'] . '/autoloader.php');

    // Plugins system init
    include_once __DIR__ . '/plugins-system-init.php';


    return $app;

};