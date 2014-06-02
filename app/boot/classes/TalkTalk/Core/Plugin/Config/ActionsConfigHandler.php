<?php

namespace TalkTalk\Core\Plugin\Config;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class ActionsConfigHandler implements PluginConfigHandlerInterface
{

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
        $actionFilePath = $plugin->path . '/actions/' . $actionData['target'] . '.php';

        return <<<PLUGIN_PHP_CODE
\$app->addAction('$urlPattern', function() use (\$app) {
    \$action = \$app->includeInApp('$actionFilePath');
    call_user_func(\$action);
})->via('$method');

PLUGIN_PHP_CODE;
    }
}