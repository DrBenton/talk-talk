<?php

namespace TalkTalk\Core\Plugin;

use TalkTalk\Core\Plugin\PackingBehaviour\PluginPackerBehaviourInterface;
use TalkTalk\Core\ApplicationAware;

class Plugin extends ApplicationAware
{

    /**
     * @var string
     */
    public $path;
    /**
     * @var string
     */
    public $entryPoint;
    /**
     * @var string
     */
    public $id;


}
