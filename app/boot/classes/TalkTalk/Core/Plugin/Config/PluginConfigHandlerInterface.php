<?php

namespace TalkTalk\Core\Plugin\Config;

use TalkTalk\Core\Plugin\UnpackedPlugin;

interface PluginConfigHandlerInterface
{

    /**
     * @param \TalkTalk\Core\Plugin\UnpackedPlugin $plugin
     * @return string
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin);

} 