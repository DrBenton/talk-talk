<?php

$app['perfs.now.time_elapsed'] = function () use ($app) {
    return round(microtime(true) - $app['perfs.start-time'], 3);
};

$app['perfs.now.nb_included_files'] = function () use ($app) {
    return count(get_included_files());
};

$app['perfs.perfs_info'] = function () use ($app) {
    $perfsInfo = array();

    // Time elapsed, for different app phases
    $perfsInfo['elapsedTimeNow'] = $app['perfs.now.time_elapsed'];
    $perfsInfo['elapsedTimeAtBootstrap'] = $app['perfs.bootstrap.time_elapsed'];
    $perfsInfo['elapsedTimeAtPluginsInit'] = $app['perfs.plugins-init.time_elapsed'];
    // Number of included files, for different app phases
    $perfsInfo['nbIncludedFilesNow'] = $app['perfs.now.nb_included_files'];
    $perfsInfo['nbIncludedFilesAtBootstrap'] = $app['perfs.bootstrap.nb_included_files'];
    $perfsInfo['nbIncludedFilesAtPluginsInit'] = $app['perfs.plugins-init.nb_included_files'];
    // Plugins-related info
    $pluginsFinder = $app['plugins.finder'];
    $perfsInfo['nbPlugins'] = $pluginsFinder->getNbPlugins();
    $perfsInfo['nbPluginsPermanentlyDisabled'] = $pluginsFinder->getNbPluginsPermanentlyDisabled();
    $perfsInfo['nbPluginsDisabledForCurrentUrl'] = $pluginsFinder->getNbPluginsDisabledForCurrentUrl();
    // Silex-related info
    $perfsInfo['nbActionsRegistered'] = $app['routes']->count();
    // SQL stuff
    $queriesLog = $app['db']->getConnection()->getQueryLog();
    $perfsInfo['nbSqlQueries'] = count($queriesLog);
    // Do we add SQL queries detail?
    if ($app['config']['debug']['perfs.tracking.display_sql_queries']) {
        $perfsInfo['sqlQueries'] = $queriesLog;
    }

    return $perfsInfo;
};
