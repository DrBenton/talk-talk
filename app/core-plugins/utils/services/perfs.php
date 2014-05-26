<?php

$app['perfs.script.duration'] = function () use ($app) {
    return round(microtime(true) - $app['perfs.start-time'], 3);
};

$app['perfs.script.nb-included-files'] = function () use ($app) {
    return count(get_included_files());
};

$app['perfs.debug-info'] = function () use ($app) {
    $debugInfo = array();

    // Plugins-related info
    $pluginsFinder = $app['plugins.finder'];
    $debugInfo['nbPlugins'] = $pluginsFinder->getNbPlugins();
    $debugInfo['nbPluginsPermanentlyDisabled'] = $pluginsFinder->getNbPluginsPermanentlyDisabled();
    $debugInfo['nbPluginsDisabledForCurrentUrl'] = $pluginsFinder->getNbPluginsDisabledForCurrentUrl();
    // Silex-related info
    $debugInfo['nbActionsRegistered'] = $app['routes']->count();

    return $debugInfo;
};
