<?php

namespace TalkTalk\CorePlugins\Core\PluginsManagerBehaviour;

use TalkTalk\Core\Plugins\Manager\Behaviour\BehaviourBase;

class TwigExtensionsManager extends BehaviourBase
{

    public function registerTwigExtensions()
    {
        foreach ($this->pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['twig-extensions'])) {
                continue;
            }

            foreach ($plugin->data['twig-extensions'] as $twigExtFileName) {
                $twigExtFilePath = $plugin->path . '/twig-ext/' . $twigExtFileName . '.php';
                $this->pluginsManager->includeFileInIsolatedClosure($twigExtFilePath);
            }
        }
    }

}
