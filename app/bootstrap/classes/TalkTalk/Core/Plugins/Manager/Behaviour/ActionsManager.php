<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

use TalkTalk\Core\Plugins\PluginData;
use TalkTalk\CorePlugins\Utils\ArrayUtils;
use Silex\Controller;

class ActionsManager extends BehaviourBase
{

    public function registerActions()
    {
        $app = $this->pluginsManager->getApp();
        foreach ($this->pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['@actions'])) {
                continue;
            }

            foreach ($plugin->data['@actions'] as $actionData) {
                if (isset($actionData['debugOnly']) && !$app['debug']) {
                    continue;
                }
                $this->registerAction($plugin, $actionData);
            }
        }
    }

    protected function registerAction(PluginData $plugin, array $actionData)
    {
        $app = $this->pluginsManager->getApp();
        $pluginsManager = $this->pluginsManager;
        $actionData['method'] = isset($actionData['method']) ? $actionData['method'] : 'GET';
        $urlsPrefix = isset($plugin->data['@general']['actionsUrlsPrefix']) ? $plugin->data['@general']['actionsUrlsPrefix'] : '';

        $controller = $app->match(
            $urlsPrefix . $actionData['url'],
            function () use ($app, $pluginsManager, $plugin, $actionData) {
                // We resolve this action controller file path...
                $actionPath = $plugin->path . '/actions/' . $actionData['target'] . '.php';
                // ...we include it in an isolated context, with only "$app" access... 
                $actionFunc = $pluginsManager->includeFileInIsolatedClosure($actionPath);
                // ...we trigger the Dependencies Injector on the returned Closure... 
                $actionArgs = $app['resolver']->getArguments(
                    $app['request'],
                    $actionFunc
                );

                // ...and we finally trigger the Closure! 
                return call_user_func_array($actionFunc, $actionArgs);
            }
        )->method($actionData['method']);

        // Route name management
        if (isset($actionData['name'])) {
            $controller->bind($actionData['name']);
        }

        // "before" middlewares management
        $this->handleActionBeforeMiddlewares($plugin, $controller, $actionData);

        $app['monolog']->addDebug(
            sprintf('Route "%s" (method %s) registered.', $actionData['url'], $actionData['method'])
        );
    }

    protected function handleActionBeforeMiddlewares (
        PluginData $plugin,
        Controller $controller,
        array $actionData
    ) {
        $app = $this->pluginsManager->getApp();
        
        // Whole Plugin "general/actionsBefore" middlewares goes first
        if (isset($plugin->data['@general']['actionsBefore'])) {
            $plugin->data['@general']['actionsBefore'] = ArrayUtils::getArray(
                $plugin->data['@general']['actionsBefore']
            );
            
            foreach ($plugin->data['@general']['actionsBefore'] as $wholePluginbeforeMiddlewareServiceName) {
                $controller->before($app[$wholePluginbeforeMiddlewareServiceName]);
            }
        }
        
        // Now, let's handle this route specific middlewares
        if (isset($actionData['before'])) {
            $actionData['before'] = ArrayUtils::getArray($actionData['before']);
            foreach ($actionData['before'] as $beforeMiddlewareServiceName) {
                $controller->before($app[$beforeMiddlewareServiceName]);
            }
        }
    }
}
