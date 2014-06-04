<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\ApplicationAware;
use TalkTalk\Core\Plugin\UnpackedPlugin;

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
    public function beforePacking(UnpackedPlugin $plugin)
    {
        // Default behaviour is to do nothing here
    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        // Default behaviour is to return nothing here
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getMetadata(UnpackedPlugin $plugin)
    {
        // Default behaviour is to return nothing here
        return null;
    }

}
