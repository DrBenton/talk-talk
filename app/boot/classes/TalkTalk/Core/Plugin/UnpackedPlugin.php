<?php

namespace TalkTalk\Core\Plugin;

use TalkTalk\Core\Plugin\Config\PluginConfigHandlerInterface;

class UnpackedPlugin
{

    protected static $configHandlers = array();

    public static function addBehaviour(PluginConfigHandlerInterface $configHandler)
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
            $code .= $configHandler->getPhpCodeToPack($this);
        }

        return $code;
    }

}