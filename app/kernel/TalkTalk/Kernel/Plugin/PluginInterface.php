<?php

namespace TalkTalk\Kernel\Plugin;

use TalkTalk\Kernel\ApplicationAwareInterface;

interface PluginInterface extends ApplicationAwareInterface
{

    public function setPath($basePath);
    public function getPath();

    public function setAssetsBaseUrl($assetsBaseUrl);
    public function setVendorsBaseUrl($jsVendorsBaseUrl);

    public function registerHooks();

    public function registerRestResources();

}