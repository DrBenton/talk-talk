<?php

namespace TalkTalk\CorePlugin\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;
use TalkTalk\Core\Plugin\PackingBehaviour\PluginPackerBehaviourInterface;

class TemplatesPacker implements PluginPackerBehaviourInterface
{

    public function init(UnpackedPlugin $plugin)
    {
        // No specific initialization phase for this Packer
    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        $code = '';

        $pluginTemplatesPath = $plugin->path . '/templates';

        if (is_dir($pluginTemplatesPath)) {
            // This Plugin has a "templates/" folder.
            // --> let's add it t the $app "view.folders" var!
            $code .= <<<PLUGIN_PHP_CODE
namespace {
    \$app->vars['view.folders'][] = array(
        'namespace' => '$plugin->id',
        'path' => '$pluginTemplatesPath',
    );
}
PLUGIN_PHP_CODE;
        }

        return $code;
    }

    /**
     * @param \TalkTalk\Core\Plugin\UnpackedPlugin $plugin
     * @return array|null
     */
    public function getMetadata(UnpackedPlugin $plugin)
    {
        return null;
    }

}