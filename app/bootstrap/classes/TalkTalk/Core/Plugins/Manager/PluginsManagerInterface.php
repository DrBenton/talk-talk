<?php

namespace TalkTalk\Core\Plugins\Manager;

use Doctrine\Common\Cache\Cache;
use Slim\Log;
use TalkTalk\Core\Application;
use TalkTalk\Core\Plugins\Manager\Behaviour\BehaviourInterface;
use TalkTalk\Core\Plugins\Plugin;

interface PluginsManagerInterface
{
    public function setApplication(Application $app);

    public function addBehaviour(BehaviourInterface $behaviour);

    public function addPlugin(Plugin $plugin);

    /**
     * @param $pluginId
     * @return \TalkTalk\Core\Plugins\Plugin
     */
    public function getPlugin($pluginId);

    /**
     * @return \Silex\Application
     */
    public function getApp();

    /**
     * @return array
     */
    public function getPlugins();

    public function includeFileInIsolatedClosure($filePath);

    public function handlePluginRelatedString(Plugin $plugin, $pluginRelatedString);

    public function setLogger(Log $logger);

    public function setCache(Cache $cache);
}
