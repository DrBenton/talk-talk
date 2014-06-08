<?php

namespace TalkTalk\CorePlugin\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;
use TalkTalk\Core\Plugin\PackingBehaviour\BasePacker;

class TemplatesPacker extends BasePacker
{

    const TEMPLATES_PATH = '%plugin-path%/templates';

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(Plugin $plugin)
    {
        $pluginTemplatesDir = $this->replace(
            self::TEMPLATES_PATH,
            array(
                '%plugin-path%' => $plugin->path,
            )
        );

        if (!is_dir($pluginTemplatesDir)) {
            return '';
        }

        // This Plugin has a "templates/" folder.
        // --> let's add it t the $app "view.folders" var!
        $pluginPhpCode = <<<'PLUGIN_PHP_CODE'

namespace {
    $app->vars['view.folders'][] = array(
        'namespace' => '%plugin-id%',
        'path' => '%plugin-templates-dir%',
    );
}

PLUGIN_PHP_CODE;

        // Job's done!
        return $this->replace(
            $pluginPhpCode,
            array(
                '%plugin-templates-dir%' => $pluginTemplatesDir,
                '%plugin-id%' => $plugin->id,
            )
        );
    }

}
