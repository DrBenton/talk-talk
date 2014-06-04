<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class ServicesPacker extends BasePacker
{
    const SERVICE_FILE_PATH = '%plugin-path%/services-init/%service-name%.php';

    protected $myConfigKey = '@services';

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        if (empty($plugin->config[$this->myConfigKey])) {
            return null;
        }

        $myConfigPart = $plugin->config[$this->myConfigKey];

        $code = '';
        foreach ($myConfigPart as $serviceData) {
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

        $serviceFileInclusionCode = $this->app
            ->getService('packing-manager')
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
