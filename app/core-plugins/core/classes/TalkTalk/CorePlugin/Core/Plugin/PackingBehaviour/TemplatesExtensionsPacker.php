<?php

namespace TalkTalk\CorePlugin\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;
use TalkTalk\Core\Plugin\PackingBehaviour\BasePacker;

class TemplatesExtensionsPacker extends BasePacker
{
    const TEMPLATE_EXT_FILE_PATH = '%plugin-path%/templates-extensions/%template-ext-name%.php';

    protected $myConfigKey = '@templates-extensions';

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        if (!isset($plugin->config[$this->myConfigKey])) {
            return null;
        }

        $myConfigPart = $plugin->config[$this->myConfigKey];

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
