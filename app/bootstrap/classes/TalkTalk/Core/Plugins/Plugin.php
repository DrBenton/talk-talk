<?php

namespace TalkTalk\Core\Plugins;

/**
 * Class Plugin
 * Stores a Plugin data, as it has been parsed in its config file.
 *
 * @package TalkTalk\Core\Plugins
 */
class Plugin
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
