<?php

//TODO: prevent access to this file to anyone through HTTP

call_user_func(
    function () {

        // Existing packs removal
        /**
         * Recursive glob
         * @see http://www.g33k-zone.org/post/2010/05/27/Fonction-glob-r%C3%A9cursive
         */
        $rglob = function($pattern='*', $path='', $flags = 0) use (&$rglob) {
            $paths=glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
            $files=glob($path.$pattern, $flags);
            foreach ($paths as $path) {
                $files=array_merge($files, $rglob($pattern, $path, $flags));
            }
            return $files;
        };
        $appPath = dirname(__DIR__);
        $packsProfilesDir = $appPath . '/var/cache/php-packs';
        $existingPackProfiles = $rglob('*.pack.php', $packsProfilesDir);
        foreach($existingPackProfiles as $packToRemove) {
            unlink($packToRemove);
        }

        // App init
        $appInitClosure = require_once __DIR__ . '/../boot/app.php';
        $app = call_user_func($appInitClosure);

        $packingManager = $app->getService('packing-manager');

        $packsProfilesDir = $app->vars['app.app_path'] . '/php-packs-profiles';
        $packsProfiles = glob($packsProfilesDir . '/*.yml');

        $replaceVars = function($srcStr) use ($app) {
            return str_replace(
                array('%app-root%', '%app-path%'),
                array($app->vars['app.root_path'], $app->vars['app.app_path']),
                $srcStr
            );
        };

        // Go! Go! Go!
        foreach($packsProfiles as $packProfileFile)
        {
            $packProfileData = \Symfony\Component\Yaml\Yaml::parse($packProfileFile);

            $baseDir = isset($packProfileData['packing']['base-dir'])
                ? $packProfileData['packing']['base-dir']
                : $app->vars['app.root_path'];
            $baseDir = $replaceVars($baseDir);

            if (isset($packProfileData['files'])) {

                // Simple PHP files to pack (classes, ...)
                $filesToPack = array_map(
                    function ($filePath) use (&$baseDir, &$replaceVars) {
                        return $baseDir . '/' . $replaceVars($filePath);
                    },
                    $packProfileData['files']
                );
                $packingManager->packPhpFiles(
                    $filesToPack,
                    $packProfileData['packing']['namespace'],
                    $packProfileData['packing']['id']
                );

            }

            if (isset($packProfileData['filesIncludedByApp'])) {

                // PHP files which will be included via "Application#includeInApp"
                $filesIncludedByAppToPack = array_map(
                    function ($filePath) use (&$baseDir, &$replaceVars) {
                        return $baseDir . '/' . $replaceVars($filePath);
                    },
                    $packProfileData['filesIncludedByApp']
                );
                $packingManager->packAppInclusions(
                    $filesIncludedByAppToPack,
                    $packProfileData['packing']['namespace'],
                    $packProfileData['packing']['id']
                );

            }
        }

    }
);
