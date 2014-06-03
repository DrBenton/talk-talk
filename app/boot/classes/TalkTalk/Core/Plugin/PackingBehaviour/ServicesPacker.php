<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class ServicesPacker implements PluginPackerBehaviourInterface
{

    const SERVICE_FILE_PATH = '%plugin-path%/services-init/%service-name%.php';

    public function init(UnpackedPlugin $plugin)
    {
        // No specific initialization phase for this Packer
    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        $myConfigPart = $plugin->config['@services'];

        $code = '';
        foreach($myConfigPart as $serviceData) {
            $code .= $this->getServicePhpCode($plugin, $serviceData);
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

    protected function getServicePhpCode(UnpackedPlugin $plugin, $serviceName)
    {
        $serviceFilePath = str_replace(
            array('%plugin-path%', '%service-name%'),
            array($plugin->path, $serviceName),
            self::SERVICE_FILE_PATH
        );

        $serviceFileInclusionCode = $plugin
            ->getAppService('packing-manager')
            ->getAppInclusionsCode(array($serviceFilePath));

        return <<<PLUGIN_PHP_CODE
$serviceFileInclusionCode

namespace {
    // Service "$serviceName" initialization:
    \$app->includeInApp('$serviceFilePath');
}
PLUGIN_PHP_CODE;
    }
}