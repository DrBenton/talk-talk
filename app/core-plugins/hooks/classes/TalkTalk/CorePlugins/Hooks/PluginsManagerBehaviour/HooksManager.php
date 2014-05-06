<?php

namespace TalkTalk\CorePlugins\Hooks\PluginsManagerBehaviour;

use TalkTalk\Core\Plugins\Manager\Behaviour\BehaviourBase;
use TalkTalk\Core\Plugins\PluginData;

class HooksManager extends BehaviourBase
{

    const DEFAULT_HOOK_PRIORITY = 0;
    const CACHE_KEY = 'talk-talk/hooks/plugins-hooks-data-structure';
    const CACHE_LIFETIME = 3600;

    protected $pluginsHooksDataStructure;
    protected $pluginsHooksDefinitions = array();

    public function triggerHook($hookName, array $hookArgs = array())
    {
        if (null === $this->pluginsHooksDataStructure) {
            // Let's create a structure where hooks are grouped by hook names!
            $this->initPluginsHooksDataStructure();
        }

        if (!isset($this->pluginsHooksDataStructure[$hookName])) {
            // No plugin implements this hook...
            return;
        }

        foreach ($this->pluginsHooksDataStructure[$hookName] as $hookData) {
            $this->triggerPluginHook($hookData['plugin_id'], $hookName, $hookArgs);
        }
    }

    /**
     * Converts our plugins array of arrays with a "hooks" key into
     * a hash, making the hooks search more efficient.
     */
    protected function initPluginsHooksDataStructure()
    {
        if ($this->cache->contains(self::CACHE_KEY)) {
            $this->pluginsHooksDataStructure = $this->cache->fetch(self::CACHE_KEY);

            return;
        }

        $this->pluginsHooksDataStructure = array();

        foreach ($this->pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['hooks'])) {
                continue;
            }

            foreach ($plugin->data['hooks'] as $pluginHookData) {
                // Let's normalize this hook data
                $normalizedPluginHookData = $this->getNormalizedHookData($plugin, $pluginHookData);
                // And now, let's store it in the hooks data structure!
                $hookName = $normalizedPluginHookData['data']['name'];
                $this->pluginsHooksDataStructure[$hookName][] = $normalizedPluginHookData;
            }
        }

        // Now, let's sort each hook definition by priority
        foreach ($this->pluginsHooksDataStructure as $hookName => &$hookDefinitions) {
            usort($hookDefinitions, array($this, 'sortHooks'));
        }

        $this->cache->save(self::CACHE_KEY, $this->pluginsHooksDataStructure, self::CACHE_LIFETIME);
    }

    protected function getNormalizedHookData(PluginData $plugin, $hookData)
    {
        if (!is_array($hookData)) {
            $hookData = array(
                'name' => (string)$hookData
            );
        }

        if (!isset($hookData['priority'])) {
            if (
                0 === strpos($hookData['name'], 'html.') &&
                isset($plugin->data['general']['htmlHooksPriority'])
            ) {
                $hookData['priority'] = $plugin->data['general']['htmlHooksPriority'];
            } else {
                $hookData['priority'] = self::DEFAULT_HOOK_PRIORITY;
            }
        }

        $hookData['priority'] = (int)$hookData['priority'];

        return array(
            'plugin_id' => $plugin->id,
            'data' => $hookData,
        );
    }

    protected function sortHooks($hookA, $hookB)
    {
        $priorityA = $hookA['data']['priority'];
        $priorityB = $hookB['data']['priority'];
        if ($priorityA > $priorityB)
            return -1;
        elseif ($priorityA < $priorityB)
            return 1;
        else
            return 0;
    }

    protected function triggerPluginHook($pluginId, $hookName, array $hookArgs)
    {
        $plugin = $this->pluginsManager->getPlugin($pluginId);

        if (!isset($this->pluginsHooksDefinitions[$plugin->path])) {
            $this->initPluginHooksDefinitions($plugin);
        }

        if (!isset($this->pluginsHooksDefinitions[$plugin->path][$hookName])) {
            throw new \RuntimeException(
                sprintf('No "%s" hook name registered for plugin "%s"!', $hookName, $plugin->path)
            );
        }

        call_user_func_array($this->pluginsHooksDefinitions[$plugin->path][$hookName], $hookArgs);
    }

    protected function initPluginHooksDefinitions(PluginData $plugin)
    {
        // We're going to inject hooks implementations in the following array:
        $hooks = array();

        // Vars injected in the hooks implementation Closure
        $app = $this->pluginsManager->getApp();
        $__pluginHooksImplementationsFilePath = $plugin->path . '/plugin-hooks.php';

        if (!file_exists($__pluginHooksImplementationsFilePath)) {
            throw new \RuntimeException(
                sprintf('Plugin "%s" hooks definition file not found!', $plugin->id)
            );
        }

        // Go! We include the "plugins-hooks.php" file: we expect it to fill our $hooks array
        call_user_func(
            function () use (&$hooks, $app, $plugin, $__pluginHooksImplementationsFilePath) {
                require_once $__pluginHooksImplementationsFilePath;
            }
        );

        // Ok, now we can store these hooks implementations in our internal array
        $this->pluginsHooksDefinitions[$plugin->path] = $hooks;
    }

}
