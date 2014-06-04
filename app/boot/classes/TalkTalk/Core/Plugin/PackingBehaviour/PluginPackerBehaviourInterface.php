<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\ApplicationAwareInterface;
use TalkTalk\Core\Plugin\UnpackedPlugin;

interface PluginPackerBehaviourInterface extends ApplicationAwareInterface
{

    /**
     * Triggered once, before Plugins packing
     * @return string|null
     */
    public function getPackerInitCode();

    /**
     * Triggered just before each Plugin packing
     * @param UnpackedPlugin $plugin
     */
    public function beforePacking(UnpackedPlugin $plugin);

    /**
     * Triggers each Plugin packing operations
     * @param  \TalkTalk\Core\Plugin\UnpackedPlugin $plugin
     * @return string|null
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin);

    /**
     * @param  \TalkTalk\Core\Plugin\UnpackedPlugin $plugin
     * @return array|null
     */
    public function getMetadata(UnpackedPlugin $plugin);

}
