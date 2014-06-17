<?php

return function (array $customConfig = array()) {

    // Core vars setup
    $kernelPath = __DIR__;
    $appPath = dirname($kernelPath);
    $rootPath = dirname($appPath);
    $appVarPath = $appPath . '/var';
    $appCachePath = $appVarPath . '/cache';
    $appVendorsPath = $rootPath . '/vendor/php';
    $kernelResourcesPath = $kernelPath . '/Resources';
    $startTime = microtime(true);

    // A constant for some security checks
    define('APP_ENVIRONMENT', true);

    // General system setup
    date_default_timezone_set('UTC');
    chdir($rootPath);

    // App config data parsing
    $mainConfigFilePath = $kernelResourcesPath . '/config/main.ini.php';
    $config = parse_ini_file($mainConfigFilePath, true);
    //$config = array_merge_recursive($config, $customConfig);

    // Composer loading
    require_once $appVendorsPath . '/autoload.php';

    // Okay, let's create our Application!
    $silexApp = new \Silex\Application();
    $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $app = new \TalkTalk\Kernel\Application($silexApp, $request);

    // App config init
    $app->setConfig($config);

    // App core vars definition
    $app->vars['app.root_path'] = $rootPath;
    $app->vars['app.app_path'] = $appPath;
    $app->vars['app.kernel_path'] = $kernelPath;
    $app->vars['app.var_path'] = $appVarPath;
    $app->vars['app.cache_path'] = $appCachePath;
    $app->vars['app.php_vendors_path'] = $appVendorsPath;
    $app->vars['app.js_vendors_path'] = $rootPath . '/vendor/js';

    $app->vars['perfs.start_time'] = $startTime;
    $app->vars['app.root_url'] = (isset($customConfig['app.root_url']))
        ? $customConfig['app.root_url']
        : $app->getRequest()->getBasePath();
    $app->vars['isAjax'] = $app->getRequest()->isXmlHttpRequest();
    $app->vars['app.http_status_code'] = 200;//everything goes well... until now :-)

    // App init! (this initializes our Kernel Services)
    \TalkTalk\Kernel\ApplicationInitializer::initCoreServices($app);

    // Some performances-related stats
    if (!empty($app->vars['config']['debug']['perfs.tracking.enabled'])) {
        $app->vars['perfs.bootstrap.elapsed_time'] = round((microtime(true) - $app->vars['perfs.start_time']) * 1000);
        $app->vars['perfs.bootstrap.nb_included_files'] = count(get_included_files());
    }

    // Plugins / Themes system init
    \TalkTalk\Kernel\ApplicationInitializer::initPlugins($app);

    // Performances-related stats, episode II
    if (!empty($app->vars['config']['debug']['perfs.tracking.enabled'])) {
        $app->vars['perfs.plugins-init.elapsed_time'] = round((microtime(true) - $app->vars['perfs.start_time']) * 1000);
        $app->vars['perfs.plugins-init.nb_included_files'] = count(get_included_files());
    }

    return $app;

};