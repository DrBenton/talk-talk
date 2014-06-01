<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

use Doctrine\Common\Cache\Cache;
use Slim\Log;
use TalkTalk\Core\Plugins\Manager\PluginsManagerInterface;

abstract class BehaviourBase implements BehaviourInterface
{
    /**
     * @var \TalkTalk\Core\Plugins\Manager\PluginsManagerInterface
     */
    protected $pluginsManager;
    /**
     * @var \Slim\Log
     */
    protected $logger;
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;
    /**
     * @var \TalkTalk\Core\Application
     */
    protected $app;

    public function setPluginsManager(PluginsManagerInterface $pluginsManager)
    {
        $this->pluginsManager = $pluginsManager;
        $this->app = $this->pluginsManager->getApp();
    }

    public function setLogger(Log $logger)
    {
        $this->logger = $logger;
    }

    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }
}
