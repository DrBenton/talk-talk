<?php

namespace TalkTalk\CorePlugin\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;
use TalkTalk\Core\Plugin\PackingBehaviour\BasePacker;
use TalkTalk\Core\Service\ArrayUtils;

class AppAssetsPacker extends BasePacker
{

    public function getPackerInitCode()
    {
        return <<<PLUGIN_PHP_CODE
namespace {
    \$app->vars['app.assets.css'] = array();
}
PLUGIN_PHP_CODE;
    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        if (!isset($plugin->config['@assets'])) {
            return null;
        }

        $myConfigPart = $plugin->config['@assets'];

        $code = '';
        if (isset($myConfigPart['stylesheets'])) {
            foreach($myConfigPart['stylesheets'] as $cssFileData) {
                $code .= $this->getAssetPhpCode($plugin, 'css', ArrayUtils::getArray($cssFileData, 'url'));
            }

        }
        if (isset($myConfigPart['javascripts'])) {
            foreach($myConfigPart['javascripts'] as $jsFileData) {
                $code .= $this->getAssetPhpCode($plugin, 'js', ArrayUtils::getArray($jsFileData, 'url'));
            }

        }

        return $code;
    }

    protected function getAssetPhpCode(UnpackedPlugin $plugin, $assetType, $assetData)
    {
        $stringUtils = $plugin->getAppService('utils.string');
        $assetData['url'] = $stringUtils->handlePluginRelatedString($plugin, $assetData['url']);

        switch ($assetType)
        {
            case 'css':
                $assetPhpCode = $this->getCssPhpCode($plugin, $assetData);
                break;
            case 'js':
                $assetPhpCode = $this->getJsPhpCode($plugin, $assetData);
                break;
        }

        return <<<PLUGIN_PHP_CODE
namespace {
    $assetPhpCode
}

PLUGIN_PHP_CODE;
    }

    protected function getCssPhpCode(UnpackedPlugin $plugin, $cssData)
    {
        $cssDataPhpCode = var_export($cssData, true);
        return <<<CSS_PHP_CODE
\$app->vars['app.assets.css'][] = $cssDataPhpCode;
CSS_PHP_CODE;
    }

    protected function getJsPhpCode(UnpackedPlugin $plugin, $jsData)
    {
        $target = (isset($jsData['head']) && true === $jsData['head']) ? 'head' : 'endOfBody';
        $jsDataPhpCode = var_export($jsData, true);
        return <<<JS_PHP_CODE
\$app->vars['app.assets.js.$target'][] = $jsDataPhpCode;
JS_PHP_CODE;
    }

}