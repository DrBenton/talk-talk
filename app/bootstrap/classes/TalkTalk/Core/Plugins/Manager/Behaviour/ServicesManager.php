<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

class ServicesManager extends BehaviourBase
{
    /**
     * @return array
     */
    public function registerPluginsServices()
    {
        foreach ($this->_pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['services'])) {
                continue;
            }

            foreach ($plugin->data['services'] as $serviceFileName) {
                $serviceFilePath = $plugin->pluginPath . '/services/' . $serviceFileName . '.php';
                $this->_pluginsManager->includeFileInIsolatedClosure($serviceFilePath);
            }
        }
    }

}
