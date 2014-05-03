<?php

namespace TalkTalk\CorePlugins\Core\PluginsManagerBehaviour;

use TalkTalk\Core\Plugins\Manager\Behaviour\BehaviourBase;

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
