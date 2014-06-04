<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class GeneralPacker  extends BasePacker
{

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        $myConfigPart = $plugin->config['@general'];

        return <<<PLUGIN_PHP_CODE
namespace {
    \$app->vars['plugins.registered_plugins'][] = '$plugin->id';
}
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