<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class NewPackersPacker implements PluginPackerBehaviourInterface
{

    public function init(UnpackedPlugin $plugin)
    {
        $myConfigPart = $plugin->config['@pluginsPackers'];

        // In this Packer we return no code, but we add the new Plugins Packing Behaviours
        // before others Packers "getPhpCodeToPack()" calls
        foreach($myConfigPart as $packerClass) {

            if (!class_exists($packerClass, false)) {
                $packerClassPath = $plugin->path . '/classes/' . str_replace('\\', DIRECTORY_SEPARATOR, $packerClass) . '.php' ;
                require_once $packerClassPath;
            }

            $packerInstance = new $packerClass();
            UnpackedPlugin::addBehaviour($packerInstance);
        }
    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        return null;
    }

    /**
     * @param \TalkTalk\Core\Plugin\UnpackedPlugin $plugin
     * @return array|null
     */
    public function getMetadata(UnpackedPlugin $plugin)
    {
        return null;
    }
}