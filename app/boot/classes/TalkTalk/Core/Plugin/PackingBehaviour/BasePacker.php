<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\ApplicationAware;
use TalkTalk\Core\Plugin\Plugin;

abstract class BasePacker extends ApplicationAware implements PluginPackerBehaviourInterface
{

    protected $myConfigKey;

    /**
     * @inheritdoc
     */
    public function getPackerInitCode()
    {
        // Default behaviour is return nothing here
        return null;
    }

    /**
     * @inheritdoc
     */
    public function beforePacking(Plugin $plugin)
    {
        // Default behaviour is to do nothing here
    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(Plugin $plugin)
    {
        // Default behaviour is to return nothing here
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getMetadata(Plugin $plugin)
    {
        // Default behaviour is to return nothing here
        return null;
    }

    /**
     * Since our Plugin Packers do a lot of String replacements,
     * let's give them a shortcut to StringUtils::replace.
     *
     * @param $string
     * @param  array  $varsMap
     * @return string
     */
    protected function replace($string, array $varsMap)
    {
        static $stringUtils;
        if (null === $stringUtils) {
            $stringUtils = $this->app->get('utils.string');
        }

        return $stringUtils->replace($string, $varsMap);
    }

    /**
     * As the Plugin Packers use the Packing Manager a lot,
     * let's give them a shortcut to it.
     *
     * @return \TalkTalk\Core\Service\PackingManager
     */
    protected function getPackingManager()
    {
        static $packingManager;
        if (null === $packingManager) {
            $packingManager = $this->app->get('packing-manager');
        }

        return $packingManager;
    }

}
