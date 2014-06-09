<?php

namespace TalkTalk\CorePlugin\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;
use TalkTalk\Core\Plugin\PackingBehaviour\BasePacker;

class TemplatesExtensionsPacker extends BasePacker
{
    const TEMPLATE_EXT_FILE_PATH = '%plugin-path%/templates-extensions/%template-ext-name%.php';

    protected $myConfigKey = '@templates-extensions';

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(Plugin $plugin)
    {
        if (empty($plugin->config[$this->myConfigKey])) {
            return null;
        }

        $myConfigPart = $plugin->config[$this->myConfigKey];

        $code = '';
        foreach ($myConfigPart as $extensionName) {
            $code .= $this->getExtensionPhpCode($plugin, $extensionName);
        }

        return $code;
    }

    protected function getExtensionPhpCode(Plugin $plugin, $extensionName)
    {
        $templateExtFilePath = $this->replace(
            self::TEMPLATE_EXT_FILE_PATH,
            array(
                '%plugin-path%' => $plugin->path,
                '%template-ext-name%' => $extensionName,
            )
        );

        $templateExtFileContent = file_get_contents($templateExtFilePath);

        $templateExtFileInclusionCode = $this->getPackingManager()
            ->stripOpeningPhpTag($templateExtFileContent);

        $pluginPhpCode = <<<'PLUGIN_PHP_CODE'

namespace {
    // Template extension "%extension-name%" initialization (from Plugin "%plugin-id%"):
    $app->before(
        function () use ($app) {
            $extension = call_user_func(
                function () use ($app) {
                    %template-ext-file-inclusion-code%
                }
            );
            if ($extension instanceof \TalkTalk\Core\ApplicationAware) {
                $extension->setApplication($app);
            }
            $app->get('view')->addExtension($extension);
        }
    );
}

PLUGIN_PHP_CODE;

        // Job's done!
        return $this->replace(
            $pluginPhpCode,
            array(
                '%template-ext-file-inclusion-code%' => $templateExtFileInclusionCode,
                '%extension-name%' => $extensionName,
                '%plugin-id%' => $plugin->id,
            )
        );
    }
}
