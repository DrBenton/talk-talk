<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

use TalkTalk\Core\Plugins\PluginData;

class HooksManager extends BehaviourBase
{

    protected $_pluginsHooksDefinitions = array();

    public function triggerHook($hookName, array $hookArgs = array())
    {
        foreach ($this->_pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['hooks'])) {
                continue;
            }

            foreach ($plugin->data['hooks'] as $pluginHookName) {
                if ($hookName === $pluginHookName) {
                    $this->triggerPluginHook($plugin, $hookName, $hookArgs);
                }
            }
        }
    }

    protected function triggerPluginHook(PluginData $plugin, $hookName, array $hookArgs)
    {
        if (!isset($this->_pluginsHooksDefinitions[$plugin->pluginPath])) {
            $this->initPluginHooksDefinitions($plugin);
        }

        if (!isset($this->_pluginsHooksDefinitions[$plugin->pluginPath][$hookName])) {
            throw new \RuntimeException(
                sprintf('No "%s" hook name registered for plugin "%s"!', $hookName, $plugin->pluginPath)
            );
        }

        call_user_func_array($this->_pluginsHooksDefinitions[$plugin->pluginPath][$hookName], $hookArgs);
    }

    protected function initPluginHooksDefinitions(PluginData $plugin)
    {
        // We're going to inject hooks implementations in the following array:
        $hooks = array();

        // Vars injected in the hooks implementation Closure
        $__pluginHooksImplementationsFilePath = $plugin->pluginPath . '/plugin-hooks.php';

        // Go! We include the "plugins-hooks.php" file: we expect it to fill our $hooks array
        call_user_func(
            function () use (&$hooks, $plugin, $__pluginHooksImplementationsFilePath) {
                include_once $__pluginHooksImplementationsFilePath;
            }
        );

        // Ok, now we can store these hooks implementations in our internal array
        $this->_pluginsHooksDefinitions[$plugin->pluginPath] = $hooks;
    }

}
