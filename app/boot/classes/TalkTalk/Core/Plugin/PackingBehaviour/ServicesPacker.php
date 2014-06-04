<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class ServicesPacker extends BasePacker
{
    const SERVICE_FILE_PATH = '%plugin-path%/services-init/%service-name%.php';

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