<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

class EventsManager extends BehaviourBase
{

    public function registerPluginsEvents()
    {
        foreach ($this->pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['@events'])) {
                continue;
            }

            foreach ($plugin->data['@events'] as $eventFileName) {
                $eventFilePath = $plugin->path . '/events/' . $eventFileName . '.php';
                $this->pluginsManager->includeFileInIsolatedClosure($eventFilePath);
            }
        }
    }

}
