<?php

namespace TalkTalk\Core\Plugin\Config;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class GeneralConfigHandler implements PluginConfigHandlerInterface
{

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        $myConfigPart = $plugin->config['@general'];

        return <<<PLUGIN_PHP_CODE
\$app->vars['plugins.registered_plugins'][] = '$plugin->id';
PLUGIN_PHP_CODE;
    }

    /**
     * @param \TalkTalk\Core\Plugin\UnpackedPlugin $plugin
     * @return array|null
     */
    public function getMetadata(UnpackedPlugin $plugin)
    {
        return array(
          'id' => $plugin->id,
          'path' => $plugin->path,
        );
    }
}