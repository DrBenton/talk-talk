<?php

namespace TalkTalk\Kernel\Plugin;

use TalkTalk\Kernel\ApplicationAwareInterface;

interface PluginInterface extends ApplicationAwareInterface
{

    public function setPath($basePath);
    public function getPath();
    public function getAbsPath();

    public function setAssetsBaseUrl($assetsBaseUrl);
    public function setVendorsBaseUrl($jsVendorsBaseUrl);

    public function registerServices();

    public function registerHooks();

    public function registerRestResources();

}