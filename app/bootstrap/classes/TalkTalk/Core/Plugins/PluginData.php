<?php

namespace TalkTalk\Core\Plugins;

class PluginData
{

    public $id;
    public $path;
    /**
     * @var array
     */
    public $data = array();

    public function __construct($pluginId, $pluginPath, array $pluginData)
    {
        $this->id = $pluginId;
        $this->path = $pluginPath;
        //TODO: sanitize plugins data (no "/ start", no "../"...)
        $this->data = $pluginData;
    }

}
