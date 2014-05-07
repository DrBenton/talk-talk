<?php

namespace TalkTalk\CorePlugins\Core\PluginsManagerBehaviour;

use TalkTalk\Core\Plugins\Manager\Behaviour\BehaviourBase;

class TwigViewsFinder extends BehaviourBase
{
    /**
     * @return array
     */
    public function getPluginsViewsPaths()
    {
        //TODO: use data cache here too
        $viewsPaths = array();
        foreach ($this->pluginsManager->getPlugins() as $plugin) {
            $pluginViewPath = $plugin->path . '/views';
            if (is_dir($pluginViewPath)) {
                $viewsPaths[] = $pluginViewPath;
            }
        }

        return $viewsPaths;
    }

}
