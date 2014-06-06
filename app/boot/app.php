<?php

return function () {

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

    // General system setup
    date_default_timezone_set('UTC');
    chdir($rootPath);

    // App config data parsing
    $mainConfigFilePath = $appPath . '/config/main.ini.php';
    $config = parse_ini_file($mainConfigFilePath, true);

    // Do we have some PHP classes packs to load early?
    $phpClassesPacks = array(
        'vendors/slim',
        'app/boot/classes',
    );
    foreach ($phpClassesPacks as $phpPack) {
        $phpPackFilePath = $appPhpPacksPath . '/' . $phpPack . '.pack.php';
        if (file_exists($phpPackFilePath)) {
            include_once $phpPackFilePath;
        }
    }

    // Composer loading - only if needed at this point!
    if (!class_exists('TalkTalk\Core\Application', false)) {
        require_once $appVendorsPath . '/autoload.php';
    }

    // Okay, let's create our Application!
    $slimApp = new \Slim\Slim();
    $app = new \TalkTalk\Core\Application($slimApp);

    $app->vars['config'] = &$config;
    $app->vars['debug'] = (bool) $app->vars['config']['debug']['debug'];
    $slimApp->config('debug', $app->vars['debug']);

    // App core vars definition
    $app->vars['app.root_path'] = $rootPath;
    $app->vars['app.app_path'] = $appPath;
    $app->vars['app.boot_path'] = $bootPath;
    $app->vars['app.var_path'] = $appVarPath;
    $app->vars['app.cache_path'] = $appCachePath;
    $app->vars['app.php_packs_path'] = $appPhpPacksPath;
    $app->vars['app.php_vendors_path'] = $appVendorsPath;
    $app->vars['app.js_vendors_path'] = $rootPath . '/vendor/js';
    $app->vars['app.boot_services_path'] = $app->vars['app.boot_path'] . '/core-services';

    $app->vars['perfs.start_time'] = $startTime;
    $app->vars['request'] = $slimApp->request;
    $app->vars['app.base_url'] = $app->vars['request']->getRootUri();
    $app->vars['isAjax'] = $app->vars['request']->isAjax();

    // Classes automatic repacking management
    if (!empty($config['debug']['packing.always_repack_profiles'])) {
        $app->after(
          function () use ($app) {
              $app->get('logger')->debug(
                  'The app "packing.always_repack_profiles" is set to \'true\': we clear & repack all Pack Profiles.'
              );
              $packingProfilesManager = $app->get('packing-profiles-manager');
              $packingProfilesManager->clearAllPackedProfiles();
              $packingProfilesManager->runAllPackProfiles();
          },
          -255
        );
    }

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
    $coreServicesToInit = array(
        'logger',
        'cache',
        'packing-manager',
        'packing-profiles-manager',
        'autoloader',
        'string-utils',
        'array-utils',
        'io-utils',
    );
    array_walk(
        $coreServicesToInit,
        function ($serviceFileName) use ($app) {
            $app->includeInApp($app->vars['app.boot_services_path'] . '/' . $serviceFileName . '.php');
        }
    );

    // Pretty errors display, if we are in debug mode
    if ($app->vars['debug'] && !empty($app->vars['config']['debug']['use_whoops_for_errors'])) {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }

    // Plugins system init
    include_once __DIR__ . '/plugins-system-init.php';

    return $app;

};
