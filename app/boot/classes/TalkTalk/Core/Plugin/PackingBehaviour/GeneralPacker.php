<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;

class GeneralPacker  extends BasePacker
{

    protected $myConfigKey = '@general';

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(Plugin $plugin)
    {
        if (empty($plugin->config[$this->myConfigKey])) {
            throw new \DomainException(sprintf('Plugin "%s" config file must have a "@general" section!'), $plugin->path);
        }

        $pluginPhpCode = <<<'PLUGIN_PHP_CODE'

namespace {
    $app->vars['plugins.registered_plugins'][] = '%plugin-id%';
}

PLUGIN_PHP_CODE;

        return $this->replace(
            $pluginPhpCode,
            array(
                '%plugin-id%' => $plugin->id,
            )
        );
    }

    /**
     * @param  \TalkTalk\Core\Plugin\Plugin $plugin
     * @return array|null
     */
    public function getMetadata(Plugin $plugin)
    {
        $pluginMetadata = array(
          'id' => $plugin->id,
          'path' => $plugin->path,
        );

        if (!empty($plugin->config['@general']['disabled'])) {
            $pluginMetadata['disabled'] = true;
        }
        if (isset($plugin->config['@general']['enabledOnlyForUrl'])) {
            $pluginMetadata['enabledOnlyForUrl'] = $plugin->config['@general']['enabledOnlyForUrl'];
        }

        return $pluginMetadata;
    }
}
