<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->after(
    function (Request $request, Response $response) use ($app) {

        if (empty($app->vars['config']['debug']['perfs.tracking.enabled'])) {
            return;
        }

        $perfsInfo = $app->get('perfs')->getAllPerfsInfo();

        $headers = array(
            'X-Perfs-Elapsed-Time-Now' => $perfsInfo['elapsedTimeNow'],
            'X-Perfs-Elapsed-Time-Bootstrap' => $perfsInfo['elapsedTimeAtBootstrap'],
            'X-Perfs-Elapsed-Time-Plugins-Init' => $perfsInfo['elapsedTimeAtPluginsInit'],
            'X-Perfs-Nb-Included-Files-Now' => $perfsInfo['nbIncludedFilesNow'],
            'X-Perfs-Nb-Included-Templates-Now' => $perfsInfo['nbIncludedTemplatesNow'],
            'X-Perfs-Nb-Included-Packs-Now' => $perfsInfo['nbIncludedPacksNow'],
            'X-Perfs-Nb-Included-Files-Bootstrap' => $perfsInfo['nbIncludedFilesAtBootstrap'],
            'X-Perfs-Nb-Included-Files-Plugins-Init' => $perfsInfo['nbIncludedFilesAtPluginsInit'],
            'X-Perfs-Nb-Plugins' => $perfsInfo['nbPlugins'],
            'X-Perfs-Nb-Actions-Registered' => $perfsInfo['nbActionsRegistered'],
            'X-Perfs-Nb-Plugins-Permanently-Disabled' => $perfsInfo['nbPluginsPermanentlyDisabled'],
            'X-Perfs-Nb-Plugins-Disabled-For-Current-URL' => $perfsInfo['nbPluginsDisabledForCurrentUrl'],
            'X-Perfs-SQL-Nb-Queries' => $perfsInfo['nbSqlQueries'],
        );

        // We add session content too, but avoid to overkill Ajax requests payload
        // if we have a lot of data in session
        $sessionContent = json_encode($app->get('session')->all());
        $sessionContentStrMaxLength = $app->vars['config']['debug']['perfs.tracking.session_content.max_length'];
        $sessionContent = (strlen($sessionContent) > $sessionContentStrMaxLength)
            ? substr($sessionContent, 0, $sessionContentStrMaxLength) . ' ... [truncated] }'
            : $sessionContent;
        $headers['X-Perfs-Session-Content'] = $sessionContent;

        // Do we send SQL queries detail?
        if (isset($perfsInfo['sqlQueries'])) {
            $sqlQueries = array_slice($perfsInfo['sqlQueries'], 0, $app->vars['config']['debug']['perfs.tracking.sql_queries.max_length']);
            $headers['X-Perfs-SQL-Queries'] = json_encode($sqlQueries);
        }

        // Optional View rendering info
        if (isset($perfsInfo['viewRenderingDuration'])) {
            $headers['X-Perfs-View-Rendering-Duration'] = $perfsInfo['viewRenderingDuration'];
        }

        // Optional QueryPath info
        if (isset($perfsInfo['queryPathDuration'])) {
            $headers['X-Perfs-QueryPath-Duration'] = $perfsInfo['queryPathDuration'];
        }

        // Optional Plugins packing info
        if (isset($perfsInfo['pluginsPackingDuration'])) {
            $headers['X-Perfs-Plugins-Packing-Duration'] = $perfsInfo['pluginsPackingDuration'];
        }

        $response->headers->add($headers);

    },
    -1 // we want to run this *after* the QueryPath "after" hook
);
