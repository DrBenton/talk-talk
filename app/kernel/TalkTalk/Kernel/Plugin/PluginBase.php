<?php

namespace TalkTalk\Kernel\Plugin;

use TalkTalk\Kernel\ApplicationInterface;

abstract class PluginBase implements PluginInterface
{

    /**
     * DO override this value in your Plugins!
     * @var string
     */
    protected $pluginId = null;
    /**
     * DO override this value in your Plugins too!
     * @var string
     */
    protected $pluginType = null;
    /**
     * =====================================================================
     * Vars values you may want to override in your Plugins classes:
     */
    /**
     * @var int
     */
    protected $jsFilesInclusionPriority = 0;
    /**
     * @var int
     */
    protected $jsFilesToCompilePriority = 0;
    /**
     * @var boolean
     */
    protected $hasJsBootstrapModule = true;


    /**
     * =====================================================================
     * And now, the internal vars. You probably don't want to override these...
     */
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

    public function __construct()
    {
        if (!is_string($this->pluginId)) {
            throw new \DomainException(sprintf('Plugin id "%s" of class "%s" is not valid!', $this->pluginId, get_class($this)));
        }

        $validPluginTypes = array(
            PluginInterface::PLUGIN_TYPE_CORE_PLUGIN,
            PluginInterface::PLUGIN_TYPE_THIRD_PARTY_PLUGIN,
            PluginInterface::PLUGIN_TYPE_THEME,
        );
        if (!in_array($this->pluginType, $validPluginTypes)) {
            throw new \DomainException(sprintf('Plugin type "%s" of class "%s" is not valid!', $this->pluginType, get_class($this)));
        }
    }

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
        $hooks = $this->app->get('hooks');

        // Default implementation maps some main app hooks to internal no-op methods,
        // for an easier main app hooks usage.
        // Just override some of these methods in your Plugins classes.

        // JS and CSS files & data
        $hooks->onHook(
            'layout.js.get_data',
            array($this, 'getJsData')
        );
        $hooks->onHook(
            'layout.js.get_scripts',
            array($this, 'getHtmlPageJsFiles'),
            $this->jsFilesInclusionPriority
        );
        $hooks->onHook(
            'layout.css.get_head_scripts',
            array($this, 'getHtmlPageCssFiles')
        );
        if ($this->hasJsBootstrapModule) {
            // Include our standardized component module in the app "boot modules"!
            $hooks->onHook(
                'layout.js.get_data',
                array($this, 'getBootstrapModuleId')
            );
        }

        // JS files to compile for production
        $hooks->onHook(
            'layout.js.get_modules_to_compile',
            array($this, 'getJsModulesToCompile'),
            $this->jsFilesToCompilePriority
        );

        // View stuff
        $hooks->onHook(
            'view.get_extensions',
            array($this, 'getViewExtensions')
        );
        $hooks->onHook(
            'view.get_templates_folders',
            array($this, 'getTemplatesFolders')
        );
    }

    public function registerRestResources()
    {
        // Default implementation is a no-op
    }

    /**
     * The returned data will be transmitted to JavaScript at JS app boot
     * @private
     * @return array
     */
    public function getJsData()
    {
        // Default implementation is a no-op
        return array();
    }

    /**
     * The returned Javascript files URLs will be included to the page
     * @private
     * @param array $opts An array of options, which can be used from the HTML template
     * @return array
     */
    public function getHtmlPageJsFiles(array $opts = array())
    {
        // Default implementation is a no-op
        return array();
    }

    /**
     * The returned CSS files URLs will be included to the page
     * @private
     * @param array $opts An array of options, which can be used from the HTML template
     * @return array
     */
    public function getHtmlPageCssFiles(array $opts = array())
    {
        // Default implementation is a no-op
        return array();
    }

    /**
     * The returned RequireJS modules Ids and their dependencies will be compiled into the app compiled JS file
     *
     * @private
     * @return array
     */
    public function getJsModulesToCompile()
    {
        // Default implementation returns the Plugin bootstrap file
        $myBootstrapModuleIdData = $this->getBootstrapModuleId();
        return array($myBootstrapModuleIdData['bootstrapModulesIds'][0]);
    }

    /**
     * An array of Plates Extensions
     *
     * @private
     * @return array
     */
    public function getViewExtensions()
    {
        // Default implementation is a no-op
        return array();
    }

    /**
     * An array of Plates folders. Each one must ne an array with "namespace" and "path" keys.
     *
     * @private
     * @return array
     */
    public function getTemplatesFolders()
    {
        // Default implementation is a no-op
        return array();
    }

    /**
     * @private
     * @return array
     */
    public function getBootstrapModuleId()
    {
        $myBootstrapModuleId = null;

        switch ($this->pluginType) {
            case PluginInterface::PLUGIN_TYPE_CORE_PLUGIN:
                $myBootstrapModuleId = sprintf('app/core-plugins/%s/assets/js/amd/components/bootstrap/main', $this->pluginId);
                break;
            case PluginInterface::PLUGIN_TYPE_THIRD_PARTY_PLUGIN:
                $myBootstrapModuleId = sprintf('plugins/%s/assets/js/amd/components/bootstrap/main', $this->pluginId);
                break;
            case PluginInterface::PLUGIN_TYPE_THEME:
                $myBootstrapModuleId = sprintf('themes/%s/assets/js/amd/components/bootstrap/main', $this->pluginId);
                break;
        }

        return array(
            'bootstrapModulesIds' => array(
                $myBootstrapModuleId
            )
        );
    }
}