<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

use TalkTalk\Core\Plugins\PluginData;
use TalkTalk\CorePlugins\Utils\ArrayUtils;

class ActionsManager extends BehaviourBase
{

    public function registerActions()
    {
        foreach ($this->pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['actions'])) {
                continue;
            }

            foreach ($plugin->data['actions'] as $actionData) {
                $this->registerAction($plugin, $actionData);
            }
        }
    }

    protected function registerAction(PluginData $plugin, array $actionData)
    {
        $app = $this->pluginsManager->getApp();
        $pluginsManager = $this->pluginsManager;
        $actionData['method'] = isset($actionData['method']) ? $actionData['method'] : 'GET';
        $urlsPrefix = isset($plugin->data['general']['actionsUrlsPrefix']) ? $plugin->data['general']['actionsUrlsPrefix'] : '';

        $route = $app->match(
            $urlsPrefix . $actionData['url'],
            function () use ($app, $pluginsManager, $plugin, $actionData) {
                $actionPath = $plugin->path . '/' . $actionData['target'];
                $actionFunc = $pluginsManager->includeFileInIsolatedClosure($actionPath);
                $actionArgs = $app['resolver']->getArguments(
                    $app['request'],
                    $actionFunc
                );

                return call_user_func_array($actionFunc, $actionArgs);
            }
        )->method($actionData['method']);

        // Route name management
        if (isset($actionData['name'])) {
            $route->bind($actionData['name']);
        }

        // "before" middlewares management
        if (isset($actionData['before'])) {
            if (isset($plugin->data['general']['actionsBefore'])) {
                // Whole Plugin "general/actionsBefore" Middlewares goes first
                $plugin->data['general']['actionsBefore'] = ArrayUtils::getArray(
                    $plugin->data['general']['actionsBefore']
                );
                foreach ($plugin->data['general']['actionsBefore'] as $wholePluginbeforeMiddlewareServiceName) {
                    $route->before($app[$wholePluginbeforeMiddlewareServiceName]);
                }

            }
            $actionData['before'] = ArrayUtils::getArray($actionData['before']);
            foreach ($actionData['before'] as $beforeMiddlewareServiceName) {
                $route->before($app[$beforeMiddlewareServiceName]);
            }
        }

        $app['monolog']->addDebug(
            sprintf('Route "%s" (method %s) registered.', $actionData['url'], $actionData['method'])
        );
    }

}
