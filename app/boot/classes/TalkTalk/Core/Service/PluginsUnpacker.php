<?php

namespace TalkTalk\Core\Service;

use TalkTalk\Core\Plugin\Plugin;

class PluginsUnpacker extends BaseService
{

    protected $packsDataNs;

    public function setPacksDataNamespace($namespace)
    {
        $this->packsDataNs = $namespace;
    }

    /**
     * @return bool
     */
    public function hasPackedPlugins()
    {

    }

    public function unpackPlugins()
    {

    }

    /**
     * @param string $pluginPackFilePath
     * @return \TalkTalk\Core\Plugin\Plugin
     */
    protected function unpackPlugin($pluginPackFilePath)
    {

    }

    protected function getPackedPluginsMetadataFile()
    {

    }

}