<?php

namespace TalkTalk\Core\Plugins;

class PluginData
{

    public $pluginPath;
    /**
     * @var array
     */
    public $data = array();

    public function __construct($pluginPath, array $pluginData)
    {
        $this->pluginPath = $pluginPath;
        $this->data = $pluginData;
    }

}
