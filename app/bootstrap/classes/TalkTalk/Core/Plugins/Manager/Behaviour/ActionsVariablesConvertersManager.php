<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

class ActionsVariablesConvertersManager extends BehaviourBase
{

    protected $converters = array();

    public function registerPluginsActionsVariablesConverters()
    {
        foreach ($this->pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['@actions-variables-converters'])) {
                continue;
            }

            foreach ($plugin->data['@actions-variables-converters'] as $converterName) {
                $this->converters[$converterName] = $plugin->path . '/actions-variables-converters/' . $converterName . '.php';
            }
        }
    }

    /**
     * @param  string   $converterName
     * @return \Closure
     */
    public function getActionVariableConverter($converterName)
    {
        if (!isset($this->converters[$converterName])) {
            throw new \RuntimeException(
                sprintf('No actions variables converter "%s" found!', $converterName)
            );
        }

        // Converters are lazy-loaded: only the current route required converters are actually included.
        $__pluginsManager = $this->pluginsManager;
        $__converterFilePath = $this->converters[$converterName];
        $converterWrapper = function ($paramValue) use ($__pluginsManager, $__converterFilePath) {
            $converterClosure = $__pluginsManager->includeFileInIsolatedClosure($__converterFilePath);

            return $converterClosure($paramValue);
        };

        return $converterWrapper;
    }

}
