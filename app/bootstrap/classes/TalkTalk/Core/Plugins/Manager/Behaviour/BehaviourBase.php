<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

use Doctrine\Common\Cache\Cache;
use Psr\Log\LoggerInterface;
use TalkTalk\Core\Plugins\Manager\PluginsManagerInterface;

class BehaviourBase implements BehaviourInterface
{
    /**
     * @var \TalkTalk\Core\Plugins\Manager\PluginsManagerInterface
     */
    protected $pluginsManager;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    public function setPluginsManager(PluginsManagerInterface $pluginsManager)
    {
        $this->pluginsManager = $pluginsManager;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }
}
