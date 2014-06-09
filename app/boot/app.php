<?php

return function (array $customConfig = array()) {

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
    //$config = array_merge_recursive($config, $customConfig);

    // Do we have some PHP classes packs to load early?
    $earlyPhpClassesPacks = array();
    if (!empty($config['packing']['use_app_packing'])) {
        // Since our App has a hard-coded dependency to Silex, we have to load the Silex "vendor pack"
        // prior to our app boot classes loading.
        $earlyPhpClassesPacks[] = 'vendors/silex';
        $earlyPhpClassesPacks[] = 'vendors/logger';
        $earlyPhpClassesPacks[] = 'app/boot/classes';
    }
    foreach ($earlyPhpClassesPacks as $phpPack) {
        $phpPackFilePath = $appPhpPacksPath . '/' . $phpPack . '.pack.php';
        if (file_exists($phpPackFilePath)) {
            include_once $phpPackFilePath;
        }
    }

    // Composer loading - only if needed at this point!
    if (!class_exists('TalkTalk\Core\Application', false) || !class_exists('Silex\Application', false)) {
        $loader = require_once $appVendorsPath . '/autoload.php';
    }

    // Okay, let's create our Application!
    $silexApp = new \Silex\Application();
    $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $app = new \TalkTalk\Core\Application($silexApp, $request);

    $app->setConfig($config);


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
    $app->vars['app.base_url'] = (isset($customConfig['app.base_url']))
        ? $customConfig['app.base_url']
        : $app->getRequest()->getBasePath();
    $app->vars['isAjax'] = $app->getRequest()->isXmlHttpRequest();
    $app->vars['app.http_status_code'] = 200;//everything goes well... until now :-)

    // Classes automatic repacking management
    if (!empty($config['debug']['packing']['always_repack_profiles'])) {
        $app->after(
          function () use ($app) {
              $app->get('logger')->debug(
                  'The app "packing.always_repack_profiles" is set to \'true\': we clear & repack all Pack Profiles.'
              );
              $packingProfilesManager = $app->get('packing-profiles-manager');
              $packingProfilesManager->clearAllPackedProfiles();
              $packingProfilesManager->runAllPackProfiles();
          },
          \TalkTalk\Core\ApplicationInterface::LATE_EVENT
        );
    }

    // Services packs management
    if (!empty($config['packing']['use_app_packing'])) {
        $servicesIncludedInAppPhpPacks = array(
            'app/boot/services',
        );
        foreach ($servicesIncludedInAppPhpPacks as $phpPack) {
            $phpPackFilePath = $appPhpPacksPath . '/' . $phpPack . '.pack.php';
            if (file_exists($phpPackFilePath)) {
                $app->includeInApp($phpPackFilePath);
            }
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
        'silex-callbacks-bridge',
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

    // Some performances-related stats
    if ($app->vars['config']['debug']['perfs.tracking.enabled']) {
        $app->vars['perfs.bootstrap.elapsed_time'] = round((microtime(true) - $app->vars['perfs.start_time']) * 1000);
        $app->vars['perfs.bootstrap.nb_included_files'] = count(get_included_files());
    }

    // Plugins system init
    include_once __DIR__ . '/plugins-system-init.php';

    // Performances-related stats, episode II
    if ($app->vars['config']['debug']['perfs.tracking.enabled']) {
        $app->vars['perfs.plugins-init.elapsed_time'] = round((microtime(true) - $app->vars['perfs.start_time']) * 1000);
        $app->vars['perfs.plugins-init.nb_included_files'] = count(get_included_files());
    }

    return $app;

};
