<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;

class ActionsParamsConvertersPacker extends BasePacker
{

    const CONVERTER_FILE_PATH = '%plugin-path%/actions-params-converters/%converter-id%.php';

    protected $myConfigKey = '@actions-params-converters';

    public function getPackerInitCode()
    {
        return <<<'PLUGIN_PHP_CODE'
namespace {
    // Actions initialization
    $app->vars['plugins.actions.params-converters'] = array();

    $app->beforeRun(
        function () use ($app) {
            $app->get('logger')->debug(
                sprintf('Actions params converters initialization (%d converters registered).', count($app->vars['plugins.actions.params-converters']))
            );

            // 2) Actions params converters are registered!
            foreach($app->vars['plugins.actions.params-converters'] as $converterId => $converterCallback) {
                $app->addActionsParamsConverter($converterId, $converterCallback);
            }
        },
        10
    );
}

PLUGIN_PHP_CODE;
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
        foreach ($myConfigPart as $converterId) {
            $code .= $this->getConverterPhpCode($plugin, $converterId);
        }

        return $code;
    }

    protected function getConverterPhpCode(Plugin $plugin, $converterId)
    {
        $converterFilePath = str_replace(
            array('%plugin-path%', '%converter-id%'),
            array($plugin->path, $converterId),
            self::CONVERTER_FILE_PATH
        );

        $converterFileInclusionCode = $this->app
            ->get('packing-manager')
            ->getAppInclusionsCode(array($converterFilePath));

        return <<<PLUGIN_PHP_CODE
$converterFileInclusionCode

namespace {
    // "$converterId" Actions Params Converter init (from Plugin "$plugin->id")
    \$app->vars['plugins.actions.params-converters']['$converterId'] = function () use (\$app) {
        return \$app->includeInApp('$converterFilePath');
    };
}

PLUGIN_PHP_CODE;
    }

}
