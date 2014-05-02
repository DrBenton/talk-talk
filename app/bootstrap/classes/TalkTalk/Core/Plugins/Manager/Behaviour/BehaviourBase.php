<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

use TalkTalk\Core\Plugins\Manager\PluginsManagerInterface;

class BehaviourBase implements BehaviourInterface
{
    /**
     * @var \TalkTalk\Core\Plugins\Manager\PluginsManagerInterface
     */
    protected $_pluginsManager;

    public function setPluginsManager(PluginsManagerInterface $pluginsManager)
    {
        $this->_pluginsManager = $pluginsManager;
    }
}
