<?php

$app['perfs.now.time_elapsed'] = function () use ($app) {
    return round(microtime(true) - $app['perfs.start-time'], 3);
};

$app['perfs.now.nb_included_files'] = function () use ($app) {
    return count(get_included_files());
};

$app['perfs.perfs_info.sql.get_connection_log'] = $app->protect(
    function ($connectionName = null) use ($app) {
        $res = array();

        $queriesLog = $app['db']->getConnection($connectionName)->getQueryLog();
        $res['nbSqlQueries'] = count($queriesLog);
        // Do we add SQL queries detail?
        if ($app['config']['debug']['perfs.tracking.sql_queries.enabled']) {
            $res['sqlQueries'] = $queriesLog;
        }

        return $res;
    }
);

$app['perfs.perfs_info'] = $app->share(
    function () use ($app) {
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
        $defaultConnectionLog = $app['perfs.perfs_info.sql.get_connection_log']();
        $perfsInfo['nbSqlQueries'] = $defaultConnectionLog['nbSqlQueries'];
        if (isset($defaultConnectionLog['sqlQueries'])) {
            $perfsInfo['sqlQueries'] = $defaultConnectionLog['sqlQueries'];
        }
        // Do we have a active phpBb connection?
        if (isset($app['phpbb.db.connection.name'])) {
            // It seems we do! Let's add its SQL queries log
            $phpbbConnectionLog = $app['perfs.perfs_info.sql.get_connection_log']($app['phpbb.db.connection.name']);
            $perfsInfo['nbSqlQueries'] += $phpbbConnectionLog['nbSqlQueries'];
            if (isset($phpbbConnectionLog['sqlQueries'])) {
                $perfsInfo['sqlQueries'] = array_merge($perfsInfo['sqlQueries'], $phpbbConnectionLog['sqlQueries']);
            }
        }

        return $perfsInfo;
    }
);
