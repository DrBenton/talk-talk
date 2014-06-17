<?php

namespace TalkTalk\Kernel\Plugin;

use TalkTalk\Kernel\ApplicationInterface;

abstract class PluginBase implements PluginInterface
{

    /**
     * @var \TalkTalk\Kernel\Application
     */
    protected $app;
    /**
     * @var string
     */
    protected $path;
    /**
     * @var string
     */
    protected $assetsBaseUrl;
    /**
     * @var string
     */
    protected $javascriptsBaseUrl;
    /**
     * @var string
     */
    protected $amdModulesBaseUrl;
    /**
     * @var string
     */
    protected $componentsBaseUrl;
    /**
     * @var string
     */
    protected $vendorsBaseUrl;

    public function setApplication(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    public function setPath($basePath)
    {
        $this->path = $basePath;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getAbsPath()
    {
        return $this->app->vars['app.root_path'] . '/' . $this->path;
    }

    public function setAssetsBaseUrl($assetsBaseUrl)
    {
        $this->assetsBaseUrl = $assetsBaseUrl;
        $this->javascriptsBaseUrl = $this->assetsBaseUrl . '/js';
        $this->amdModulesBaseUrl = preg_replace('~^/~', '', $this->javascriptsBaseUrl) . '/amd';
        $this->componentsBaseUrl = $this->amdModulesBaseUrl . '/components';
    }

    public function setVendorsBaseUrl($jsVendorsBaseUrl)
    {
        $this->vendorsBaseUrl = $jsVendorsBaseUrl;
    }

    public function registerServices()
    {
        // Default implementation is a no-op
    }

    public function registerHooks()
    {
        // Default implementation is a no-op
    }

    public function registerRestResources()
    {
        // Default implementation is a no-op
    }
}