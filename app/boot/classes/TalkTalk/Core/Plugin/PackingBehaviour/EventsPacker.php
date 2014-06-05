<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class EventsPacker extends BasePacker
{
    const EVENT_FILE_PATH = '%plugin-path%/events/%event-name%.php';

    protected $myConfigKey = '@events';

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
        foreach ($myConfigPart as $eventName) {
            $code .= $this->getEventPhpCode($plugin, $eventName);
        }

        return $code;
    }

    protected function getEventPhpCode(UnpackedPlugin $plugin, $eventName)
    {
        $eventFilePath = str_replace(
            array('%plugin-path%', '%event-name%'),
            array($plugin->path, $eventName),
            self::EVENT_FILE_PATH
        );

        $serviceFileInclusionCode = $this->app
            ->get('packing-manager')
            ->getAppInclusionsCode(array($eventFilePath));

        return <<<PLUGIN_PHP_CODE
$serviceFileInclusionCode

namespace {
    // Event "$eventName" initialization:
    \$app->includeInApp('$eventFilePath');
}
PLUGIN_PHP_CODE;
    }
}
