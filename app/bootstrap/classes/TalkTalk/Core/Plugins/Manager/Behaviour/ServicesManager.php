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
                $serviceFilePath = $plugin->path . '/services-init/' . $serviceFileName . '.php';
                $serviceDefinition = $this->app->includeFileInIsolatedClosure($serviceFilePath);

                if (!is_a($serviceDefinition, 'TalkTalk\Core\Services\ServiceDefinition')) {
                    throw new \RuntimeException(sprintf('Invalid Service definition returned for Service "%s"!', $serviceFileName));
                }
            }
        }
    }

}
