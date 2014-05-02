<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

class ClassesManager extends BehaviourBase
{
    /**
     * @return array
     */
    public function registerClassLoadingSchemes()
    {
        $pluginsManager = $this->_pluginsManager;
        $app = $pluginsManager->getApp();

        foreach ($this->_pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['classes'])) {
                continue;
            }

            foreach ($plugin->data['classes'] as $serviceClassMapScheme) {

                // Paths setup
                $paths = $serviceClassMapScheme['paths'];
                if (is_string($paths))
                    $paths = array($paths);
                foreach ($paths as $index => $path) {
                    $paths[$index] = str_replace(
                        $app['app.path'] . '/',
                        '',
                        $pluginsManager->handlePluginRelatedString($plugin, $path)
                    );
                }

                // Go! Classes prefixes are mapped to theses paths, in PSR4 mode
                $app['autoloader']->addPsr4(
                    $serviceClassMapScheme['prefix'],
                    $paths
                );
            }
        }
    }

}
