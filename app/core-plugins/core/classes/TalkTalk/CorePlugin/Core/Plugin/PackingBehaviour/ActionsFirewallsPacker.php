<?php

namespace TalkTalk\CorePlugin\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;
use TalkTalk\Core\Plugin\PackingBehaviour\BasePacker;

class ActionsFirewallsPacker extends BasePacker
{
    const FIREWALL_FILE_PATH = '%plugin-path%/firewalls/%firewall-id%.php';

    protected $myConfigKey = '@firewalls';

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
        foreach ($myConfigPart as $firewallId) {
            $code .= $this->getFirewallPhpCode($plugin, $firewallId);
        }

        return $code;
    }

    protected function getFirewallPhpCode(Plugin $plugin, $firewallId)
    {
        $firewallFilePath = $this->replace(
            self::FIREWALL_FILE_PATH,
            array(
                '%plugin-path%' => $plugin->path,
                '%firewall-id%' => $firewallId,
            )
        );

        $firewallFileInclusionCode = $this->getPackingManager()
            ->getAppInclusionsCode(array($firewallFilePath));

        $pluginPhpCode = <<<'PLUGIN_PHP_CODE'

%firewall-file-inclusion-code%

namespace {
    // Firewall "%firewall-id%" initialization (from Plugin "%plugin-id%"):
    $firewallId = 'firewall-' . '%firewall-id%';
    $silexCallbacksBridge = $app->getService('silex.callbacks_bridge');

    $silexCallbacksBridge->registerCallback(
        $firewallId,
        function () use ($app) {
            return $app->includeInApp('%firewall-file-path%');
        }
    );
}

PLUGIN_PHP_CODE;

        // Job's done!
        return $this->replace(
            $pluginPhpCode,
            array(
                '%firewall-file-inclusion-code%' => $firewallFileInclusionCode,
                '%firewall-file-path%' => $firewallFilePath,
                '%firewall-id%' => $firewallId,
                '%plugin-id%' => $plugin->id,
            )
        );
    }
}
