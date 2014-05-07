<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

class ServicesManager extends BehaviourBase
{

    public function registerPluginsServices()
    {
        foreach ($this->pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['@services'])) {
                continue;
            }

            foreach ($plugin->data['@services'] as $serviceFileName) {
                $serviceFilePath = $plugin->path . '/services/' . $serviceFileName . '.php';
                $this->pluginsManager->includeFileInIsolatedClosure($serviceFilePath);
            }
        }
    }

}
