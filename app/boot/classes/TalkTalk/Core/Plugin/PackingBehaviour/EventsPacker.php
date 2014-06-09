<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;

class EventsPacker extends BasePacker
{
    const EVENT_FILE_PATH = '%plugin-path%/events/%event-name%.php';

    protected $myConfigKey = '@events';

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
        foreach ($myConfigPart as $eventName) {
            $code .= $this->getEventPhpCode($plugin, $eventName);
        }

        return $code;
    }

    protected function getEventPhpCode(Plugin $plugin, $eventName)
    {
        $eventFilePath = $this->replace(
            self::EVENT_FILE_PATH,
            array(
                '%plugin-path%' => $plugin->path,
                '%event-name%' => $eventName,
            )
        );

        $eventFileInclusionCode = $this->getPackingManager()
            ->getAppInclusionsCode(array($eventFilePath));

        $pluginPhpCode = <<<'PLUGIN_PHP_CODE'

%event-file-inclusion-code%

namespace {
    // Event "%event-name%" initialization (from Plugin "%plugin-id%"):
    $app->includeInApp('%event-file-path%');
}

PLUGIN_PHP_CODE;

        // Job's done!
        return $this->replace(
            $pluginPhpCode,
            array(
                '%event-file-inclusion-code%' => $eventFileInclusionCode,
                '%event-file-path%' => $eventFilePath,
                '%event-name%' => $eventName,
                '%plugin-id%' => $plugin->id,
            )
        );
    }
}
