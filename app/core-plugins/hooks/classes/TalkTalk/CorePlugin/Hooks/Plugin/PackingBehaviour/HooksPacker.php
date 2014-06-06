<?php

namespace TalkTalk\CorePlugin\Hooks\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;
use TalkTalk\Core\Plugin\PackingBehaviour\BasePacker;

class HooksPacker extends BasePacker
{

    const HOOKS_FILE_PATH = '%plugin-path%/plugin-hooks.php';
    const DEFAULT_HOOK_PRIORITY = 0;
    const PLUGIN_COMPONENTS_URL = '%plugin-url%/assets/js/modules/components';

    protected $myConfigKey = '@hooks';

    public function getPackerInitCode()
    {
        return <<<'PACKER_INIT_CODE'
namespace {

    /* Plugins hooks system initialization */
    $app->vars['hooks.registry.implementations'] = array();

    $app->defineFunction(
        'hooks.load_plugin_hooks',
        function ($pluginId, $hooksFilePath, $pluginComponentsUrl) use ($app) {
            if (isset($app->vars['hooks.registry.implementations'][$pluginId])) {
                // This Plugin hooks implementations have already be loaded
                return;
            }
            if (!isset($app->vars['packs.included_files.closures'][$hooksFilePath])) {
                throw new \DomainException(
                    sprintf('Hooks file "%s" not included in packed PHP code!', $hooksFilePath)
                );
            }
            $hooks = array();
            call_user_func_array(
                $app->vars['packs.included_files.closures'][$hooksFilePath],
                array($app, &$hooks, $pluginComponentsUrl)
            );
            $app->vars['hooks.registry.implementations'][$pluginId] = &$hooks;
        }
    );
}

PACKER_INIT_CODE;

    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(Plugin $plugin)
    {
        if (empty($plugin->config[$this->myConfigKey])) {
            return null;
        }

        $hooksFilePath = str_replace(
            '%plugin-path%',
            $plugin->path,
            self::HOOKS_FILE_PATH
        );

        if (!is_file($hooksFilePath)) {
            throw new \DomainException(
                sprintf('Plugin "%s" defines hooks but does not have a "%s" hooks implementation file!', $plugin->id, $hooksFilePath)
            );
        }

        $myConfigPart = $plugin->config[$this->myConfigKey];

        // Let's load the Plugin hooks implementation code!
        $pluginHooksImplementations = $this->getPluginHooksImplementations($plugin, $hooksFilePath);
        if (!is_array($pluginHooksImplementations)) {
            print_r($pluginHooksImplementations);
            throw new \DomainException(
                sprintf('Plugin "%s" hooks implementations file is malformed!', $plugin->id)
            );
        }

        $code = '';
        foreach ($myConfigPart as $hookData) {

            // Let's normalize this hook data
            $normalizedPluginHookData = $this->getNormalizedHookData($plugin, $hookData);
            $hookName = $normalizedPluginHookData['name'];

            if (!isset($pluginHooksImplementations[$hookName])) {
                print_r($pluginHooksImplementations);
                throw new \DomainException(
                    sprintf('Plugin "%s" registers a "%s" hook, but this hook definition can not be found in the %d Plugin hooks implementations!', $plugin->id, $hookName, count($pluginHooksImplementations))
                );
            }
            if (!is_callable($pluginHooksImplementations[$hookName])) {
                throw new \DomainException(
                    sprintf('Plugin "%s" registers a "%s" hook, but this hook definition is not callable!', $plugin->id, $hookName)
                );
            }

            // All right, this hooks seems to have an implementation. Let's add some
            // code to link this Plugin hook to its implementation
            $code .= $this->getHookPhpCode($plugin, $normalizedPluginHookData, $hooksFilePath);
        }

        // We add the Plugin hooks implementation code to the app
        $code .= $this->getPluginHooksImplementationsCode($hooksFilePath);

        return $code;
    }

    /**
     * @param Plugin $plugin
     * @param array $hookData
     * @param string $hooksFilePath
     * @return string
     */
    protected function getHookPhpCode(Plugin $plugin, array $hookData, $hooksFilePath)
    {
        $pluginComponentsUrl = $this->getPluginComponentsUrl($plugin);

        $hookName = $hookData['name'];

        return <<<PLUGIN_PHP_CODE
namespace {
    /* begin "$plugin->id" Plugin hook "$hookName" plug to app */
    \$app->vars['hooks.registry']['$hookName'][] = array(
        'implementation' => function (\$hookArgs) use (\$app) {
            \$app->execFunction(
                'hooks.load_plugin_hooks',
                '$plugin->id', '$hooksFilePath', '$pluginComponentsUrl'
            );
            return call_user_func_array(
                \$app->vars['hooks.registry.implementations']['$plugin->id']['$hookName'],
                \$hookArgs
            );
        },
        'priority' => $hookData[priority]
    );
    /* end "$plugin->id" Plugin hook "$hookName" plug to app */
}

PLUGIN_PHP_CODE;
    }

    /**
     * @param Plugin $plugin
     * @param string $hooksFilePath
     * @return array
     */
    protected function getPluginHooksImplementations(Plugin $plugin, $hooksFilePath)
    {
        // This Plugin has a "plugin-hooks.php" file.
        // --> let's load it!

        $pluginHooksImplementations = array();
        $pluginComponentsUrl = $this->getPluginComponentsUrl($plugin);
        call_user_func_array(
            function ($app, array &$hooks, $myComponentsUrl) use ($hooksFilePath) {
                include_once $hooksFilePath;
            },
            array($this->app, &$pluginHooksImplementations, $pluginComponentsUrl)
        );

        return $pluginHooksImplementations;
    }

    /**
     * @param string $hooksFilePath
     * @return string
     */
    protected function getPluginHooksImplementationsCode($hooksFilePath)
    {
        $hooksImplementationsInclusionCode = $this->app
            ->get('packing-manager')
            ->getAppInclusionsCode($hooksFilePath, array('&$hooks', '$myComponentsUrl'));

        return $hooksImplementationsInclusionCode;
    }

    protected function getPluginComponentsUrl(Plugin $plugin)
    {
        return $this->app
            ->get('utils.string')
            ->handlePluginRelatedString($plugin, self::PLUGIN_COMPONENTS_URL);
    }

    protected function getNormalizedHookData(Plugin $plugin, $hookData)
    {
        $hookData = $this->app->get('utils.array')->getArray($hookData, 'name');

        if (!isset($hookData['priority'])) {
            if (
                0 === strpos($hookData['name'], 'html.') &&
                isset($plugin->data['@general']['htmlHooksPriority'])
            ) {
                $hookData['priority'] = $plugin->data['@general']['htmlHooksPriority'];
            } else {
                $hookData['priority'] = self::DEFAULT_HOOK_PRIORITY;
            }
        }

        $hookData['priority'] = (int) $hookData['priority'];

        return $hookData;
    }

}