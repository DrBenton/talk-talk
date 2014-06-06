<?php

//TODO: prevent access to this file to anyone through HTTP

return call_user_func(
    function () {

        $startTime = microtime(true);
        $isCli = (php_sapi_name() === 'cli');

        // Existing packs removal
        // We have to do this manually, since we don't have the whole app code at this point.
        if (!class_exists('TalkTalk\Core\Service\IOUtils', false)) {
            require_once __DIR__ . '/../boot/classes/TalkTalk/Core/ApplicationInterface.php';
            require_once __DIR__ . '/../boot/classes/TalkTalk/Core/ApplicationAwareInterface.php';
            require_once __DIR__ . '/../boot/classes/TalkTalk/Core/ApplicationAware.php';
            require_once __DIR__ . '/../boot/classes/TalkTalk/Core/Service/BaseService.php';
            require_once __DIR__ . '/../boot/classes/TalkTalk/Core/Service/IOUtils.php';
        }
        $ioUtils = new TalkTalk\Core\Service\IOUtils();
        $appPath = dirname(__DIR__);
        $packsProfilesDir = $appPath . '/var/cache/php-packs';
        $existingPackProfilesPaths = $ioUtils->rglob('*.pack.php', $packsProfilesDir);
        array_walk($existingPackProfilesPaths, function ($path) {
            unlink($path);
        });
        $nbExistingPackProfilesRemoved = count($existingPackProfilesPaths);
        // Packs metadata removal
        @unlink($packsProfilesDir . '/packs-metadata.php');

        // We could need some config data before our app needs it
        $mainConfigFilePath = $appPath . '/config/main.ini.php';
        $appConfig = parse_ini_file($mainConfigFilePath, true);

        // App environment init
        $customAppConfig = array();
        if (isset($appConfig['packing']['base_url'])) {
            $customAppConfig['app.base_url'] = $appConfig['packing']['base_url'];
        }

        if ($isCli) {
            // Slim will not not like the CLI context, as there will be some missing $_SERVER vars.
            // --> let's disable PHP Notices!
            $previousErrorReportingLevel = error_reporting(E_ALL & ~E_NOTICE);
        }
        $appInitClosure = require_once __DIR__ . '/../boot/app.php';
        $app = call_user_func($appInitClosure, $customAppConfig);
        if ($isCli) {
            // Back to previous PHP error reporting level
            error_reporting($previousErrorReportingLevel);
        }

        // Let's set a "base_url" in our Request to mimic a HTTP context
        // Some packing operations, like URL injection into Plugins packed code, will use it
        //$app->vars['app.base_url'] = $app->vars['config']['packing']['base_url'];

        // Packing Services init
        $packingProfilesManager = $app->getService('packing-profiles-manager');
        $packingProfilesManager->clearAllPackedProfiles();
        $packsProfiles = $packingProfilesManager->runAllPackProfiles();

        $returnedData = array(
            'nbPacksRemoved' => $nbExistingPackProfilesRemoved,
            'nbPackedProfiles' => count($packsProfiles),
            'nbPackedPlugins' => $app->vars['plugins.packing.nb_packed'],
            'duration' => round((microtime(true) - $startTime) * 1000),
        );

        if ($isCli && false !== strpos(__FILE__, $GLOBALS['argv'][0])) {
            die(json_encode($returnedData) . PHP_EOL);
        } else {
            return $returnedData;
        }

    }
);
