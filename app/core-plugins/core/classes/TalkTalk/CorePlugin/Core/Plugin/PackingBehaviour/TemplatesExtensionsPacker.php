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
        $templateExtFilePath = str_replace(
            array('%plugin-path%', '%template-ext-name%'),
            array($plugin->path, $extensionName),
            self::TEMPLATE_EXT_FILE_PATH
        );

        $templateExtFileContent = file_get_contents($templateExtFilePath);

        $templateExtFileInclusionCode = $this->app
            ->get('packing-manager')
            ->stripOpeningPhpTag($templateExtFileContent);

        $templateExtFileInclusionCode = preg_replace('~^~m', '            ', $templateExtFileInclusionCode);

        return <<<PLUGIN_PHP_CODE
namespace {
    // Template extension "$extensionName" initialization:
    \$app->before(
        function () use (\$app) {
            \$extension = call_user_func(
                function () use (\$app) {
                    $templateExtFileInclusionCode
                }
            );
            if (\$extension instanceof \TalkTalk\Core\ApplicationAware) {
                \$extension->setApplication(\$app);
            }
            \$app->get('view')->addExtension(\$extension);
        }
    );
}

PLUGIN_PHP_CODE;
    }
}
