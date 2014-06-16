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
            // Let's set a "base_url" in our Request to mimic a HTTP context
            // Some packing operations, like URL injection into Plugins packed code, will use it
            $customAppConfig['app.base_url'] = $appConfig['packing']['base_url'];
        }

        $appInitClosure = require_once __DIR__ . '/../boot/app.php';
        $app = call_user_func($appInitClosure, $customAppConfig);

        // Packing Services init
        $app->vars['config']['packing']['use_vendors_packing'] = false;
        if (!$app->hasService('packing-manager')) {
            $app->includeInApp($app->vars['app.boot_services_path'] . '/packing-manager.php');
        }
        if (!$app->hasService('packing-profiles-manager')) {
            $app->includeInApp($app->vars['app.boot_services_path'] . '/packing-profiles-manager.php');
        }
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
