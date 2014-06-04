<?php

namespace TalkTalk\Core\Plugin;

use TalkTalk\Core\Plugin\PackingBehaviour\PluginPackerBehaviourInterface;
use TalkTalk\Core\ApplicationAware;

class UnpackedPlugin extends ApplicationAware
{

    protected static $packingBehaviours = array();
    protected static $packingBehavioursInitialized = false;

    public static function addBehaviour(PluginPackerBehaviourInterface $configHandler)
    {
        self::$packingBehaviours[] = $configHandler;
    }

    public static function getBehaviours()
    {
        return self::$packingBehaviours;
    }

    /**
     * @var string
     */
    public $path;
    /**
     * @var array
     */
    public $config;
    /**
     * @var array
     */
    public $pluginsPackingBehaviours = array();
    /**
     * @var string
     */
    public $id;

    public function beforePacking()
    {
        // Plugins packing behaviours init, just before packing
        // (this is in case of a Packer which would have something to do before others, like the NewPackersPacker)
        foreach(self::$packingBehaviours as $packingBehaviour) {
            $packingBehaviour->beforePacking($this);
        }
    }

    /**
     * @return string
     */
    public function getPhpCodeToPack()
    {
        $code = '';

        // Plugins packing behaviours packing!
        foreach(self::$packingBehaviours as $packingBehaviour)
        {
            $pluginPhpCodeForCurrentConfigHandler = $packingBehaviour->getPhpCodeToPack($this);

            if (null !== $pluginPhpCodeForCurrentConfigHandler) {
                $code .= PHP_EOL .
                    sprintf('/* Begin PHP code generated for plugin "%s" by Packing Behaviour "%s" */', $this->path, get_class($packingBehaviour)) . PHP_EOL .
                    $pluginPhpCodeForCurrentConfigHandler . PHP_EOL .
                    sprintf('/* End PHP code generated for plugin "%s" by Packing Behaviour "%s" */', $this->path, get_class($packingBehaviour)) . PHP_EOL
                ;
            }

        }

        return $code;
    }

    /**
     * @return array
     */
    public function getMetadataToPack()
    {
        $metadata = array();
        foreach(self::$packingBehaviours as $configHandler)
        {
            $pluginMetadataForCurrentConfigHandler = $configHandler->getMetadata($this);
            if (null !== $pluginMetadataForCurrentConfigHandler) {
                $metadata[] = $pluginMetadataForCurrentConfigHandler;
            }
        }

        return call_user_func_array('array_merge', $metadata);
    }

    public function getAppService($serviceId)
    {
        return $this->app->getService($serviceId);
    }

}