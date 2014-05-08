<?php

$app['plugins.trigger_hook'] = $app->protect(
    function ($hookName, array $hookArgs = array()) use ($app) {
        return $app['plugins.manager']->triggerHook($hookName, $hookArgs);
    }
);
