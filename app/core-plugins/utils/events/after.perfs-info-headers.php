<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->after(
    function (Request $request, Response $response) use ($app) {

        if (!$app['config']['debug']['perfs.tracking']) {
            return;
        }

        $debugInfo = $app['perfs.debug-info'];

        $response->headers->add(array(
            'X-Perfs-Duration' => $app['perfs.script.duration'],
            'X-Perfs-Bootstrap-Duration' => $app['perfs.bootstrap.duration'],
            'X-Perfs-Plugins-Init-Duration' => $app['perfs.plugins-init.duration'],
            'X-Perfs-Script-Nb-Included-Files' => $app['perfs.script.nb-included-files'],
            'X-Perfs-Bootstrap-Nb-Included-Files' => $app['perfs.bootstrap.nb-included-files'],
            'X-Perfs-Plugins-Init-Nb-Included-Files' => $app['perfs.plugins-init.nb-included-files'],
            'X-Perfs-Session-Content' => json_encode($app['session']->all()),
            'X-Perfs-Nb-Plugins' => $debugInfo['nbPlugins'],
            'X-Perfs-Nb-Actions-Registered' => $debugInfo['nbActionsRegistered'],
            'X-Perfs-Nb-Plugins-Permanently-Disabled' => $debugInfo['nbPluginsPermanentlyDisabled'],
            'X-Perfs-Nb-Plugins-Disabled-For-Current-URL' => $debugInfo['nbPluginsDisabledForCurrentUrl'],
        ));

        if (isset($app['perfs.querypath.duration'])) {
            $response->headers->set(
                'X-Perfs-QueryPath-Duration',
                $app['perfs.querypath.duration']
            );
        }
    },
    -1 // we want to run this *after* the QueryPath "after" hook
);
