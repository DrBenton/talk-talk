<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;

interface PluginPackerBehaviourInterface
{

    public function init(UnpackedPlugin $plugin);

    /**
     * @param \TalkTalk\Core\Plugin\UnpackedPlugin $plugin
     * @return string
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin);

    /**
     * @param \TalkTalk\Core\Plugin\UnpackedPlugin $plugin
     * @return array|null
     */
    public function getMetadata(UnpackedPlugin $plugin);

} 