<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->after(
    function (Request $request, Response $response) use ($app) {

        if (!$app['config']['debug']['perfs.tracking']) {
            return;
        }

        $perfsInfo = $app['perfs.perfs_info'];

        $response->headers->add(array(
            'X-Perfs-Elapsed-Time-Now' => $perfsInfo['elapsedTimeNow'],
            'X-Perfs-Elapsed-Time-Bootstrap' => $perfsInfo['elapsedTimeAtBootstrap'],
            'X-Perfs-Elapsed-Time-Plugins-Init' => $perfsInfo['elapsedTimeAtPluginsInit'],
            'X-Perfs-Nb-Included-Files-Now' => $perfsInfo['nbIncludedFilesNow'],
            'X-Perfs-Nb-Included-Files-Bootstrap' => $perfsInfo['nbIncludedFilesAtBootstrap'],
            'X-Perfs-Nb-Included-Files-Plugins-Init' => $perfsInfo['nbIncludedFilesAtPluginsInit'],
            'X-Perfs-Nb-Plugins' => $perfsInfo['nbPlugins'],
            'X-Perfs-Nb-Actions-Registered' => $perfsInfo['nbActionsRegistered'],
            'X-Perfs-Nb-Plugins-Permanently-Disabled' => $perfsInfo['nbPluginsPermanentlyDisabled'],
            'X-Perfs-Nb-Plugins-Disabled-For-Current-URL' => $perfsInfo['nbPluginsDisabledForCurrentUrl'],
        ));

        // We add session content too, but avoid to overkill Ajax requests payload
        // if we have a lot of data in session
        $sessionContent = json_encode($app['session']->all());
        $sessionContentStrMaxLength = 250;
        $sessionContent = (strlen($sessionContent) > $sessionContentStrMaxLength)
            ? substr($sessionContent, 0, $sessionContentStrMaxLength) . ' ... [truncated] }'
            : $sessionContent;
        $response->headers->set('X-Perfs-Session-Content', $sessionContent);

        if (isset($app['perfs.querypath.duration'])) {
            $response->headers->set(
                'X-Perfs-QueryPath-Duration',
                $app['perfs.querypath.duration']
            );
        }
    },
    -1 // we want to run this *after* the QueryPath "after" hook
);
