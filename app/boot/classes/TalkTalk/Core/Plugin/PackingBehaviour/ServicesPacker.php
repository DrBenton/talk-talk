<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;

class ServicesPacker extends BasePacker
{
    const SERVICE_FILE_PATH = '%plugin-path%/services/%service-name%.php';

    protected $myConfigKey = '@services';

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
        foreach ($myConfigPart as $serviceName) {
            $code .= $this->getServicePhpCode($plugin, $serviceName);
        }

        return $code;
    }

    protected function getServicePhpCode(Plugin $plugin, $serviceName)
    {
        $serviceFilePath = $this->replace(
            self::SERVICE_FILE_PATH,
            array(
                '%plugin-path%' => $plugin->path,
                '%service-name%' => $serviceName,
            )
        );

        $serviceFileInclusionCode = $this->getPackingManager()
            ->getAppInclusionsCode(array($serviceFilePath));

        $pluginPhpCode = <<<'PLUGIN_PHP_CODE'

%service-file-inclusion-code%

namespace {
    // Service "%service-name%" initialization (from Plugin "%plugin-id%"):
    $app->includeInApp('%service-file-path%');
}

PLUGIN_PHP_CODE;

        // Job's done!
        return $this->replace(
            $pluginPhpCode,
            array(
                '%service-file-inclusion-code%' => $serviceFileInclusionCode,
                '%service-file-path%' => $serviceFilePath,
                '%service-name%' => $serviceName,
                '%plugin-id%' => $plugin->id,
            )
        );
    }
}
