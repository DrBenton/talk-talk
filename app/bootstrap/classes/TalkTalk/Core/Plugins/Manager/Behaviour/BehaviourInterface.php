<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

use Doctrine\Common\Cache\Cache;
use Slim\Log;
use TalkTalk\Core\Plugins\Manager\PluginsManagerInterface;

interface BehaviourInterface
{
    public function setPluginsManager(PluginsManagerInterface $pluginsManager);

    public function setLogger(Log $logger);

    public function setCache(Cache $cache);
}
