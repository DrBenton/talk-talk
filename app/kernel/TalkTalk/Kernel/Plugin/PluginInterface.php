<?php

namespace TalkTalk\Kernel\Plugin;

use TalkTalk\Kernel\ApplicationAwareInterface;

interface PluginInterface extends ApplicationAwareInterface
{

    const PLUGIN_TYPE_CORE_PLUGIN = 'core-plugin';
    const PLUGIN_TYPE_THIRD_PARTY_PLUGIN = 'plugin';
    const PLUGIN_TYPE_THEME = 'theme';

    public function setPath($basePath);
    public function getPath();
    public function getAbsPath();

    public function setAssetsBaseUrl($assetsBaseUrl);
    public function setVendorsBaseUrl($jsVendorsBaseUrl);

    public function registerServices();

    public function registerHooks();

    public function registerRestResources();

}