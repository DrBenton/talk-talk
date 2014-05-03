<?php

namespace TalkTalk\Core\Plugins\Manager;

use Silex\Application;
use TalkTalk\Core\Plugins\Manager\Behaviour\BehaviourInterface;
use TalkTalk\Core\Plugins\PluginData;

interface PluginsManagerInterface
{
    public function setApplication(Application $app);

    public function addBehaviour(BehaviourInterface $behaviour);

    public function addPlugin(PluginData $plugin);

    /**
     * @return \Silex\Application
     */
    public function getApp();

    /**
     * @return array
     */
    public function getPlugins();

    public function includeFileInIsolatedClosure($filePath);

    public function handlePluginRelatedString(PluginData $plugin, $pluginRelatedString);
}
