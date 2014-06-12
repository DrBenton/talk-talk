<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;

class ActionsParamsConvertersPacker extends BasePacker
{

    const CONVERTER_FILE_PATH = '%plugin-path%/actions-params-converters/%converter-id%.php';

    protected $myConfigKey = '@actions-params-converters';

    public function getPackerInitCode()
    {
        return <<<'PACKER_INIT_PHP_CODE'

namespace {
    // Actions initialization
    $app->vars['plugins.actions.params-converters'] = array();

    $app->beforeRun(
        function () use ($app) {
            $converters = &$app->vars['plugins.actions.params-converters'];

            $app->get('logger')->debug(
                sprintf('Actions params converters initialization (%d converters registered).', count($converters))
            );

            // 2) Actions params converters are registered!
            foreach ($converters as $converterId => $converterCallback) {

                if (!preg_match('~^[a-z][-a-z0-9_]+$~i', $converterId)) {
                    throw new \DomainException(sprintf('Converter id "%s" is not correct (as they are converters to class methods, their name must follow the PHP classes methods naming restrictions)', $converterId));
                }

                $converterId = 'converter-' . $converterId;
                $app->getService('silex.callbacks_bridge')
                    ->registerCallback($converterId, $converterCallback);
            }
        },
        10
    );
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
        foreach ($myConfigPart as $converterId) {
            $code .= $this->getConverterPhpCode($plugin, $converterId);
        }

        return $code;
    }

    protected function getConverterPhpCode(Plugin $plugin, $converterId)
    {
        $converterFilePath = $this->replace(
            self::CONVERTER_FILE_PATH,
            array(
                '%plugin-path%' => $plugin->path,
                '%converter-id%' => $converterId,
            )
        );

        $converterFileInclusionCode = $this->getPackingManager()
            ->getAppInclusionsCode(array($converterFilePath));

        $pluginPhpCode = <<<'PLUGIN_PHP_CODE'

%converter-file-inclusion-code%

namespace {
    // "%converter-id%" Actions Params Converter init (from Plugin "%plugin-id%")
    $app->vars['plugins.actions.params-converters']['%converter-id%'] = function () use ($app) {
        return $app->includeInApp('%converter-file-path%');
    };
}

PLUGIN_PHP_CODE;

        return $this->replace(
            $pluginPhpCode,
            array(
                '%converter-id%' => $converterId,
                '%converter-file-inclusion-code%' => $converterFileInclusionCode,
                '%converter-file-path%' => $converterFilePath,
                '%plugin-id%' => $plugin->id,
            )
        );
    }

}
