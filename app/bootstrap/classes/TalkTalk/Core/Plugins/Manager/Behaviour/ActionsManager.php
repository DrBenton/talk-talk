<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use TalkTalk\Core\Plugins\Plugin;
use TalkTalk\Core\Utils\ArrayUtils;
use Silex\Controller;

class ActionsManager extends BehaviourBase
{

    public function registerActions()
    {
        $app = $this->app;
        $appActions = array();

        foreach ($this->pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['@actions'])) {
                continue;
            }

            foreach ($plugin->data['@actions'] as $actionData) {
                if (isset($actionData['onlyForDebug']) && !$app->config('debug')) {
                    continue;
                }

                $appActions[] = array(
                    'plugin' => $plugin,
                    'actionData' => $actionData,
                );
            }
        }

        // All right, we now have all our app Plugins actions in an array.
        // 1) Let's sort them by priority (higher priorities comes first)...
        usort($appActions, array($this, 'sortPluginsActions'));

        // 2) ...and register them!
        foreach ($appActions as $action) {
            $this->registerAction($action['plugin'], $action['actionData']);
        }
    }

    protected function sortPluginsActions(array $actionA, array $actionB)
    {
        $priorityA = isset($actionA['actionData']['priority']) ? $actionA['actionData']['priority'] : 0;
        $priorityB = isset($actionB['actionData']['priority']) ? $actionB['actionData']['priority'] : 0;
        if ($priorityA < $priorityB) {
            return -1;
        } elseif ($priorityA > $priorityB) {
            return 1;
        } else {
            return 0;
        }
    }

    protected function registerAction(Plugin $plugin, array &$actionData)
    {
        $actionData['method'] = isset($actionData['method'])
            ? $actionData['method']
            : 'GET';
        $urlsPrefix = isset($plugin->data['@general']['actionsUrlsPrefix'])
            ? $plugin->data['@general']['actionsUrlsPrefix']
            : '';

        $that = & $this; //PHP 5.3: old school JS "this" scope management, yeah ^^
        $controller = $this->app->match(
            $urlsPrefix . $actionData['url'],
            function () use (&$that, $plugin, $actionData) {
                return $that->runAction($plugin, $actionData);
            }
        );

        // HTTP method management
        $controller->method($actionData['method']);

        // Route name management
        if (isset($actionData['name'])) {
            $controller->bind($actionData['name']);
        }

        // Route "before" middlewares management
        $this->handleActionBeforeMiddlewares($plugin, $controller, $actionData);

        // Route requirements management
        $this->handleActionRequirements($plugin, $controller, $actionData);

        // Route variables converters management
        $this->handleActionVariablesConverters($plugin, $controller, $actionData);

        /*
         * TODO: add a optional "verbose" mode to Behaviours
        $app['monolog']->addDebug(
            sprintf('Route "%s" (method %s) registered.', $actionData['url'], $actionData['method'])
        );
        */
    }

    /**
     * @param  string                                                       $actionFilePath the file path, without the ".php" extension (will be appended automatically)
     * @throws \Symfony\Component\Security\Core\Exception\DisabledException
     * @return mixed
     */
    public function runActionFile($actionFilePath)
    {
        $actionFilePath .= '.php';

        // A small security check: we only allow action files inside the app
        $actionFilePath = realpath($actionFilePath);
        if (0 !== strpos($actionFilePath, $this->app['app.path'])) {
            throw new DisabledException(sprintf('Action file path "%s" is not inside app directory!', $actionFilePath));
        }

        // We include the file in an isolated context, with only "$app" access...
        $actionFunc = $this->pluginsManager->includeFileInIsolatedClosure($actionFilePath);

        // ...we trigger the Dependencies Injector on the returned Closure...
        $actionArgs = $this->app['resolver']->getArguments(
            $this->app['request'],
            $actionFunc
        );

        // ...and we finally trigger the action Closure!
        return call_user_func_array($actionFunc, $actionArgs);
    }

    /**
     * Only used in an internal context by a Closure which need "public" access.
     *
     * @private
     * @param  Plugin $plugin
     * @param  array  $actionData
     * @return mixed
     */
    public function runAction(Plugin $plugin, array $actionData)
    {
        // We resolve this action controller (i.e. a Closure) file path...
        $actionFilePath = $this->getActionPath($plugin, $actionData);

        // ...and we run it!
        return $this->runActionFile($actionFilePath);
    }

    /**
     * @param  Plugin $plugin
     * @param  array  $actionData
     * @return string
     */
    protected function getActionPath(Plugin $plugin, array $actionData)
    {
        $app = $this->app;

        $targetFile = $actionData['target'];

        // We may have dynamic params in the target file; let's handle them!
        $targetFile = preg_replace_callback(
            '/\{([a-z]+)\}/i',
            function ($matches) use ($app, $actionData) {
                $paramName = $matches[1];

                // Security check: we do want a "Action requirement" for this param!
                if (
                    !isset($actionData['requirements']) ||
                    !in_array($paramName, array_keys($actionData['requirements']))
                ) {
                    throw new InvalidParameterException(
                        sprintf('Action file dynamic parameter "%s" must have a requirement!', $paramName)
                    );
                }

                return $app['request']->get($paramName, '');
            },
            $targetFile
        );

        $actionPath = $plugin->path . '/actions/' . $targetFile;

        return $actionPath;
    }

    protected function handleActionBeforeMiddlewares(
        Plugin $plugin,
        Controller $controller,
        array $actionData
    )
    {
        $app = $this->app;

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

    protected function handleActionRequirements(
        Plugin $plugin,
        Controller $controller,
        array $actionData
    )
    {
        if (isset($actionData['requirements'])) {
            foreach ($actionData['requirements'] as $argName => $pattern) {
                $controller->assert($argName, $pattern);
            }
        }
    }

    protected function handleActionVariablesConverters(
        Plugin $plugin,
        Controller $controller,
        array $actionData
    )
    {
        if (isset($actionData['converters'])) {
            foreach ($actionData['converters'] as $argName => $converterName) {
                $controller->convert(
                    $argName,
                    $this->pluginsManager->getActionVariableConverter($converterName)
                );
            }
        }
    }
}
