<?php

namespace TalkTalk\CorePlugin\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;
use TalkTalk\Core\Plugin\PackingBehaviour\BasePacker;

class TemplatesExtensionsPacker extends BasePacker
{
    const TEMPLATE_EXT_FILE_PATH = '%plugin-path%/templates-extensions/%template-ext-name%.php';

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        $myConfigPart = $plugin->config['@templates-extensions'];

        $code = '';
        foreach($myConfigPart as $extensionName) {
            $code .= $this->getExtensionPhpCode($plugin, $extensionName);
        }

        return $code;
    }

    protected function getExtensionPhpCode(UnpackedPlugin $plugin, $extensionName)
    {
        $templateExtFilePath = str_replace(
            array('%plugin-path%', '%template-ext-name%'),
            array($plugin->path, $extensionName),
            self::TEMPLATE_EXT_FILE_PATH
        );

        $templateExtFileContent = file_get_contents($templateExtFilePath);

        $templateExtFileInclusionCode = $plugin
            ->getAppService('packing-manager')
            ->stripOpeningPhpTag($templateExtFileContent);

        $templateExtFileInclusionCode = preg_replace('~^~m', '            ', $templateExtFileInclusionCode);
        return <<<PLUGIN_PHP_CODE
namespace {
    // Template extension "$extensionName" initialization:
    \$extension = call_user_func(
        function () use (\$app) {
            $templateExtFileInclusionCode
        }
    );
    \$app->getService('view')->addExtension(\$extension);
}

PLUGIN_PHP_CODE;
    }
}
