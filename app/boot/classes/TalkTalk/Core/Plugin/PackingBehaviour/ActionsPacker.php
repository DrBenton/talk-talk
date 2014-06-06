<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;

class ActionsPacker extends BasePacker
{

    const ACTION_FILE_PATH = '%plugin-path%/actions/%action-target%.php';

    protected $myConfigKey = '@actions';

    public function getPackerInitCode()
    {
        return <<<'PLUGIN_PHP_CODE'
namespace {
    // Actions initialization
    $app->vars['plugins.actions'] = array();
    $app->vars['plugins.actions.names'] = array();

    $app->before(
        function () use ($app) {
            $app->get('logger')->debug(
                sprintf('Actions initialization (%d actions registered).', count($app->vars['plugins.actions']))
            );

            $actions = $app->vars['plugins.actions'];

            // 1) Actions are sorted by priority
            $actionsSorter = function (array $actionA, array $actionB)
            {
                $priorityA = $actionA['priority'];
                $priorityB = $actionB['priority'];
                if ($priorityA > $priorityB) {
                    return -1;
                } elseif ($priorityA < $priorityB) {
                    return 1;
                } else {
                    return 0;
                }
            };
            usort($actions, $actionsSorter);

            // 2) Actions are registered!
            foreach($actions as $action) {
                call_user_func($action['actionRegistering']);
            }
        },
        -10 // this "before" will be run... well... before "normal" befores
    );
}

PLUGIN_PHP_CODE;
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
            ? $actionData['method'] //TODO: handle multiple methods
            : 'GET';
        $actionFilePath = str_replace(
            array('%plugin-path%', '%action-target%'),
            array($plugin->path, $actionData['target']),
            self::ACTION_FILE_PATH
        );
        $actionPriority = isset($actionData['priority'])
            ? (int) $actionData['priority']
            : 0;

        // Advanced action settings
        $beforeActionDefinition = '';
        $afterActionDefinition = '';

        // Route name management
        if (isset($actionData['name'])) {
            $actionName = $actionData['name'];
            $beforeActionDefinition .= "
            if (in_array('$actionName', \$app->vars['plugins.actions.names'])) {
                \$app->get('logger')->debug('Action \"$actionName\" not added to Slim router, as this route name is already registered (plugin \"$plugin->id\").');
                return;//Slim don't authorize named routes overriding; let's stop here!
            }
            ";
            $afterActionDefinition .= "
            \$action->name('$actionName');
            \$app->vars['plugins.actions.names'][] = '$actionName';
            \$app->get('logger')->debug('Action \"$actionName\" added to Slim router (plugin \"$plugin->id\").');
            ";
        }

        return <<<PLUGIN_PHP_CODE
namespace {
    \$app->vars['plugins.actions'][] = array(
        'actionRegistering' => function () use (\$app) {
            $beforeActionDefinition

            \$action = \$app->addAction('$urlPattern', function () use (\$app) {
                \$action = \$app->includeInApp('$actionFilePath');

                return call_user_func(\$action);
            })
            ->via('$method');

            $afterActionDefinition
        },
        'priority' => $actionPriority
    );

}

PLUGIN_PHP_CODE;
    }

}
