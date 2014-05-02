<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

class TwigExtensionsManager extends BehaviourBase
{
    /**
     * @return array
     */
    public function registerTwigExtensions()
    {
        foreach ($this->_pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['twig-extensions'])) {
                continue;
            }

            foreach ($plugin->data['twig-extensions'] as $twigExtFileName) {
                $twigExtFilePath = $plugin->pluginPath . '/twig-ext/' . $twigExtFileName . '.php';
                $this->_pluginsManager->includeFileInIsolatedClosure($twigExtFilePath);
            }
        }
    }

}
