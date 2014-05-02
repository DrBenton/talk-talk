<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

use TalkTalk\Core\Plugins\PluginData;

class ActionsManager extends BehaviourBase
{
    /**
     * @return array
     */
    public function registerActions()
    {
        foreach ($this->_pluginsManager->getPlugins() as $plugin) {
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
        $app = $this->_pluginsManager->getApp();
        $pluginsManager = $this->_pluginsManager;
        $actionData['method'] = isset($actionData['method']) ? $actionData['method'] : 'GET';

        $route = $app->match(
            $actionData['url'],
            function () use ($app, $pluginsManager, $plugin, $actionData) {
                $actionPath = $plugin->pluginPath . '/' . $actionData['target'];
                $actionFunc = $pluginsManager->includeFileInIsolatedClosure($actionPath);
                $actionArgs = $app['resolver']->getArguments(
                    $app['request'],
                    $actionFunc
                );

                return call_user_func_array($actionFunc, $actionArgs);
            }
        )->method($actionData['method']);

        if (isset($actionData['name'])) {
            $route->bind($actionData['name']);
        }

        $app['monolog']->addDebug(sprintf('Route "%s" (method %s) registered.', $actionData['url'], $actionData['method']));
    }

}
