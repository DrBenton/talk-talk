<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;

class ActionsPacker extends BasePacker
{

    const ACTION_FILE_PATH = '%plugin-path%/actions/%action-target%.php';

    protected $myConfigKey = '@actions';

    public function getPackerInitCode()
    {
        return <<<'PACKER_INIT_PHP_CODE'

namespace {
    // Actions initialization
    $app->vars['plugins.actions'] = array();
    $app->vars['plugins.actions.names'] = array();

    $app->beforeRun(
        function () use ($app) {
            $app->get('logger')->debug(
                sprintf('Actions initialization (%d actions registered).', count($app->vars['plugins.actions']))
            );

            $actions = $app->vars['plugins.actions'];

            // 1) Actions are sorted by priority
            $app->get('utils.array')->sortBy($actions, 'priority');

            // 2) Actions are registered!
            foreach($actions as $action) {
                call_user_func($action['actionRegistering']);
            }
        }
    );
}

PACKER_INIT_PHP_CODE;
    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(Plugin $plugin)
    {
        if (empty($plugin->config[$this->myConfigKey])) {
            return null;
        }

        $myConfigPart = $plugin->config[$this->myConfigKey];

        // Does this Plugin have a general "actionsUrlsPrefix" setting?
        $urlsPrefix = (!empty($plugin->config['@general']['actionsUrlsPrefix']))
            ? $plugin->config['@general']['actionsUrlsPrefix']
            : '';

        $code = '';
        foreach ($myConfigPart as $actionData) {
            $code .= $this->getActionPhpCode($plugin, $actionData, $urlsPrefix);
        }

        return $code;
    }

    protected function getActionPhpCode(Plugin $plugin, array $actionData, $urlsPrefix)
    {
        // Bare actions stuff
        $urlPattern = $urlsPrefix . $actionData['url'];
        $method = isset($actionData['method'])
            ? $actionData['method']
            : 'GET';
        $actionFilePath = $this->replace(
            self::ACTION_FILE_PATH,
            array(
                '%plugin-path%' => $plugin->path,
                '%action-target%' => $actionData['target'],
            )
        );
        $actionPriority = isset($actionData['priority'])
            ? (int) $actionData['priority']
            : 0;

        // Base placeholders
        $codePlaceholders = array(
            '%url-pattern%' => $urlPattern,
            '%action-file-path%' => $actionFilePath,
            '%method%' => $method,
            '%action-priority%' => $actionPriority,
            '%plugin-id%' => $plugin->id,
        );

        // Advanced action definitions setup
        $beforeActionDefinitions = '';
        $afterActionDefinitions = '';

        // "onlyForDebug" setting management (if necessary)
        $this->handleOnlyForDebug($plugin, $actionData, $beforeActionDefinitions, $afterActionDefinitions, $codePlaceholders);

        // Action name management (if necessary)
        $this->handleActionName($plugin, $actionData, $beforeActionDefinitions, $afterActionDefinitions, $codePlaceholders);

        // Actions params converters management (if necessary)
        $this->handleActionParamsConverters($plugin, $actionData, $beforeActionDefinitions, $afterActionDefinitions, $codePlaceholders);

        // Actions firewalls management (if necessary)
        $this->handleActionFirewalls($plugin, $actionData, $beforeActionDefinitions, $afterActionDefinitions, $codePlaceholders);

        // All right, let's compute all this PHP code!
        $pluginPhpCode = <<<'PLUGIN_ACTIONS_PHP_CODE'

namespace {
    $app->vars['plugins.actions'][] = array(
        'actionRegistering' => function () use ($app) {
            %before-action-definitions%

            // Action registration (from Plugin "%plugin-id%")
            $action = $app->addAction('%url-pattern%', function () use ($app) {
                $action = $app->includeInApp('%action-file-path%');

                // We trigger the Dependencies Injector on the returned Closure...
                $silexApp = $app->get('silex');
                $actionArgs = $silexApp['resolver']->getArguments(
                    $app->getRequest(),
                    $action
                );

                // ...and we finally trigger the action Closure!
                return call_user_func_array($action, $actionArgs);
            });
            $action->method('%method%');

            %after-action-definitions%
        },
        'priority' => %action-priority%
    );

}

PLUGIN_ACTIONS_PHP_CODE;

        $pluginPhpCode = $this->replace($pluginPhpCode, array(
            '%before-action-definitions%' => $beforeActionDefinitions,
            '%after-action-definitions%' => $afterActionDefinitions,
        ));

        // Job's done!
        return $this->replace(
            $pluginPhpCode,
            $codePlaceholders
        );
    }

    protected function handleOnlyForDebug(
        Plugin $plugin, array $actionData,
        &$beforeActionDefinitions, &$afterActionDefinitions, &$codePlaceholders
    ) {
        if (empty($actionData['onlyForDebug'])) {
            return;
        }

        $beforeActionDefinitions .= <<<'ONLY_FOR_DEBUG_BEFORE_CODE'

            if (!$app->vars['debug']) {
                // This action is only active in "debug" mode!
                return;
            }

ONLY_FOR_DEBUG_BEFORE_CODE;
    }

    protected function handleActionName(
        Plugin $plugin, array $actionData,
        &$beforeActionDefinitions, &$afterActionDefinitions, &$codePlaceholders
    ) {
        if (!isset($actionData['name'])) {
            return;
        }

        $actionName = $actionData['name'];

        $beforeActionDefinitions .= <<<'ACTION_NAME_BEFORE_CODE'

            if (in_array('%action-name%', $app->vars['plugins.actions.names'])) {
                $app->get('logger')->debug('Action "%action-name%" not added to router, as this route name is already registered (plugin "%plugin-id%").');
                return;
            }

ACTION_NAME_BEFORE_CODE;

        $afterActionDefinitions .= <<<'ACTION_NAME_AFTER_CODE'

            $action->bind('%action-name%');
            $app->vars['plugins.actions.names'][] = '%action-name%';

ACTION_NAME_AFTER_CODE;

        $codePlaceholders['%action-name%'] = $actionName;
    }

    protected function handleActionParamsConverters(
        Plugin $plugin, array $actionData,
        &$beforeActionDefinitions, &$afterActionDefinitions, &$codePlaceholders
    ) {
        if (!isset($actionData['params-converters'])) {
            return;
        }

        $stringUtils = $this->app->get('utils.string');

        foreach ($actionData['params-converters'] as $paramName => $converterId) {

            $converterId = $stringUtils->camelize('converter-' . $converterId);

            $converterCode =  <<<'ACTION_CONVERTER_AFTER_CODE'

                $action->convert(
                    '%param-name%',
                    'talk_talk_callbacks:%converter-id%'
                );

ACTION_CONVERTER_AFTER_CODE;

            $afterActionDefinitions .= $this->replace(
                $converterCode,
                array(
                    '%param-name%' => $paramName,
                    '%converter-id%' => $converterId,
                )
            );

        }
    }

    protected function handleActionFirewalls(
        Plugin $plugin, array $actionData,
        &$beforeActionDefinitions, &$afterActionDefinitions, &$codePlaceholders
    ) {
        if (!isset($actionData['firewalls'])) {
            return;
        }

        $stringUtils = $this->app->get('utils.string');

        foreach ($actionData['firewalls'] as $firewallId) {

            $firewallId = $stringUtils->camelize('firewall-' . $firewallId);

            $firewallCode =  <<<'ACTION_FIREWALL_AFTER_CODE'

                $action->before(
                    'talk_talk_callbacks:%firewall-id%'
                );

ACTION_FIREWALL_AFTER_CODE;

            $afterActionDefinitions .= $this->replace(
                $firewallCode,
                array(
                    '%firewall-id%' => $firewallId,
                )
            );

        }
    }

}
