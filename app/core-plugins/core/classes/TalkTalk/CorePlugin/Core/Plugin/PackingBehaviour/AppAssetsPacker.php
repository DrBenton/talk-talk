<?php

namespace TalkTalk\CorePlugin\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;
use TalkTalk\Core\Plugin\PackingBehaviour\BasePacker;

class AppAssetsPacker extends BasePacker
{

    protected $myConfigKey = '@assets';

    public function getPackerInitCode()
    {
        return <<<'PACKER_INIT_PHP_CODE'

namespace {
    $app->vars['app.assets.css'] = array();
}

PACKER_INIT_PHP_CODE;
    }

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
        if (isset($myConfigPart['stylesheets'])) {
            foreach ($myConfigPart['stylesheets'] as $cssFileData) {
                $code .= $this->getAssetPhpCode(
                    $plugin,
                    'css',
                    $this->app->get('utils.array')->getArray($cssFileData, 'url')
                );
            }

        }
        if (isset($myConfigPart['javascripts'])) {
            foreach ($myConfigPart['javascripts'] as $jsFileData) {
                $code .= $this->getAssetPhpCode(
                    $plugin,
                    'js',
                    $this->app->get('utils.array')->getArray($jsFileData, 'url')
                );
            }

        }

        return $code;
    }

    protected function getAssetPhpCode(Plugin $plugin, $assetType, $assetData)
    {
        $assetData['url'] = $this->app
            ->get('utils.string')
            ->handlePluginRelatedString($plugin, $assetData['url']);

        switch ($assetType) {
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

    protected function getCssPhpCode(Plugin $plugin, $cssData)
    {
        $cssDataPhpCode = var_export($cssData, true);

        return <<<CSS_PHP_CODE

\$app->vars['app.assets.css'][] = $cssDataPhpCode;

CSS_PHP_CODE;
    }

    protected function getJsPhpCode(Plugin $plugin, $jsData)
    {
        $target = (isset($jsData['head']) && true === $jsData['head']) ? 'head' : 'endOfBody';
        $jsDataPhpCode = var_export($jsData, true);

        return <<<JS_PHP_CODE

\$app->vars['app.assets.js.$target'][] = $jsDataPhpCode;

JS_PHP_CODE;
    }

}
