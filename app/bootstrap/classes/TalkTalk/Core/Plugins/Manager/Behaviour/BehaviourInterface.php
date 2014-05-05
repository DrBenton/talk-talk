<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

use Doctrine\Common\Cache\Cache;
use Psr\Log\LoggerInterface;
use TalkTalk\Core\Plugins\Manager\PluginsManagerInterface;

interface BehaviourInterface
{
    public function setPluginsManager(PluginsManagerInterface $pluginsManager);

    public function setLogger(LoggerInterface $logger);

    public function setCache(Cache $cache);
}
