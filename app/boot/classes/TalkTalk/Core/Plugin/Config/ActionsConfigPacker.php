<?php

namespace TalkTalk\Core\Plugin\Config;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class ActionsConfigPacker implements PluginConfigPackerInterface
{

    const ACTION_FILE_PATH = '%plugin-path%/actions/%action-target%.php';

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        $myConfigPart = $plugin->config['@actions'];

        $code = '';
        foreach($myConfigPart as $actionData) {
            $code .= $this->getActionPhpCode($plugin, $actionData);
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

    protected function getActionPhpCode(UnpackedPlugin $plugin, array $actionData)
    {
        $urlPattern = $actionData['url'];
        $method = isset($actionData['method'])
            ? $actionData['method'] //TODO: handle multiple methods
            : 'GET';
        $actionFilePath = str_replace(
            array('%plugin-path%', '%action-target%'),
            array($plugin->path, $actionData['target']),
            self::ACTION_FILE_PATH
        );

        return <<<PLUGIN_PHP_CODE
namespace {
    \$app->addAction('$urlPattern', function() use (\$app) {
        \$action = \$app->includeInApp('$actionFilePath');
        return call_user_func(\$action);
    })->via('$method');
}
PLUGIN_PHP_CODE;
    }
}