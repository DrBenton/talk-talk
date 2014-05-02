<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

use TalkTalk\Core\Plugins\Manager\PluginsManagerInterface;

interface BehaviourInterface
{
    public function setPluginsManager (PluginsManagerInterface $pluginsManager);

}
