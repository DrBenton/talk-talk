<?php

namespace TalkTalk\CorePlugin\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;
use TalkTalk\Core\Plugin\PackingBehaviour\BasePacker;

class TemplatesPacker extends BasePacker
{

    const TEMPLATES_PATH = '%plugin-path%/templates';

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        $code = '';

        $pluginTemplatesPath = str_replace(
            '%plugin-path%',
            $plugin->path,
            self::TEMPLATES_PATH
        );

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

}
