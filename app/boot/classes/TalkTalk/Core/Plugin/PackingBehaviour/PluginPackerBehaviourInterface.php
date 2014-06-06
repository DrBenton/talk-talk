<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\ApplicationAwareInterface;
use TalkTalk\Core\Plugin\Plugin;

interface PluginPackerBehaviourInterface extends ApplicationAwareInterface
{

    /**
     * Triggered once, before Plugins packing
     * @return string|null
     */
    public function getPackerInitCode();

    /**
     * Triggered just before each Plugin packing
     * @param Plugin $plugin
     */
    public function beforePacking(Plugin $plugin);

    /**
     * Triggers each Plugin packing operations
     * @param  \TalkTalk\Core\Plugin\Plugin $plugin
     * @return string|null
     */
    public function getPhpCodeToPack(Plugin $plugin);

    /**
     * @param  \TalkTalk\Core\Plugin\Plugin $plugin
     * @return array|null
     */
    public function getMetadata(Plugin $plugin);

}
