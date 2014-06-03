<?php

namespace TalkTalk\Core\Plugin;

use TalkTalk\Core\Plugin\Config\PluginConfigPackerInterface;
use TalkTalk\Core\ApplicationAware;

class UnpackedPlugin extends ApplicationAware
{

    protected static $configHandlers = array();

    public static function addBehaviour(PluginConfigPackerInterface $configHandler)
    {
        self::$configHandlers[] = $configHandler;
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
    public $pluginsConfigHandlers = array();
    /**
     * @var string
     */
    public $id;

    /**
     * @return string
     */
    public function getPhpCodeToPack()
    {
        $code = '';
        foreach(self::$configHandlers as $configHandler)
        {
            $pluginPhpCodeForCurrentConfigHandler = $configHandler->getPhpCodeToPack($this);
            if (null !== $pluginPhpCodeForCurrentConfigHandler) {
                $code .= PHP_EOL .
                    sprintf('/* PHP code generated for plugin "%s" by Config Handler "%s" */', $this->path, get_class($configHandler)) . PHP_EOL .
                    $pluginPhpCodeForCurrentConfigHandler . PHP_EOL ;
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
        foreach(self::$configHandlers as $configHandler)
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