<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

class TwigViewsFinder extends BehaviourBase
{
    /**
     * @return array
     */
    public function getPluginsViewsPaths()
    {
        $viewsPaths = array();
        foreach ($this->_pluginsManager->getPlugins() as $plugin) {
            $pluginViewPath = $plugin->pluginPath . '/views';
            if (is_dir($pluginViewPath)) {
                $viewsPaths[] = $pluginViewPath;
            }
        }

        return $viewsPaths;
    }

}
