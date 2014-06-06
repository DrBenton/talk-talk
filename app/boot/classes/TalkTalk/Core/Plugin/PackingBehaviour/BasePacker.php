<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\ApplicationAware;
use TalkTalk\Core\Plugin\Plugin;

abstract class BasePacker extends ApplicationAware implements PluginPackerBehaviourInterface
{

    protected $myConfigKey;

    /**
     * @inheritdoc
     */
    public function getPackerInitCode()
    {
        // Default behaviour is return nothing here
        return null;
    }

    /**
     * @inheritdoc
     */
    public function beforePacking(Plugin $plugin)
    {
        // Default behaviour is to do nothing here
    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(Plugin $plugin)
    {
        // Default behaviour is to return nothing here
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getMetadata(Plugin $plugin)
    {
        // Default behaviour is to return nothing here
        return null;
    }

}
