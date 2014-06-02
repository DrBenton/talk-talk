<?php

namespace TalkTalk\Core\Plugin\Config;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class CoreConfigHandler implements PluginConfigHandlerInterface
{

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        $myConfigPart = $plugin->config['@general'];

        $plugin->id = $myConfigPart['id'];

        return var_export(array('id' => $plugin->id), true);
    }
}