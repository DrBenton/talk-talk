<?php

namespace TalkTalk\CorePlugin\Core;

use TalkTalk\CorePlugin\Core\Controller\BaseController;
use TalkTalk\Kernel\Plugin\PluginBase;
use TalkTalk\Kernel\ApplicationInterface;
use TalkTalk\Kernel\Plugin\PluginInterface;

class CorePlugin extends PluginBase
{

    protected $pluginId = 'core';
    protected $pluginType = PluginInterface::PLUGIN_TYPE_CORE_PLUGIN;
    protected $jsFilesInclusionPriority = ApplicationInterface::EARLY_EVENT;
    protected $jsFilesToCompilePriority = ApplicationInterface::EARLY_EVENT;

    public function setApplication(ApplicationInterface $app)
    {
        parent::setApplication($app);

        // As the "Core Plugin", I'm in charge of setting the static Controllers app instance.
        // With great power come you-know-what...
        BaseController::setApplication($this->app);
    }

    public function registerServices()
    {
        $app = &$this->app;

        // "view" Service
        $this->app->defineService('view', function () use ($app) {
            $service = new Service\View();
            $service->setTemplatesFilesExtension('tpl.php');

            return $service;
        });

        // "db" Service
        // As we have to link the database lazy connection to Illuminate Models immediately,
        // this Service is not lazy-loaded.
        $dbService = new Service\Database();
        $dbService->setApplication($this->app);
        $this->app->defineService('db',  $dbService);

        // "cache" Service
        $this->app->defineService('cache', function () use ($app) {
            $service = new Service\Cache();

            return $service;
        });
    }

    public function registerRestResources()
    {
        parent::registerRestResources();

        $NS = 'TalkTalk\\CorePlugin\\Core\\Controller';
        $this->app->addRestResource('GET', '/', "$NS\\HomeController::home");
    }

    /**
     * @inheritdoc
     */
    public function getJsData()
    {
        return array(
          'debug' => $this->app->vars['debug'],
          'rootUrl' => $this->app->vars['app.root_url'],
          'vendorsRootUrl' => $this->vendorsBaseUrl,
        );
    }

    /**
     * @inheritdoc
     */
    public function getHtmlPageJsFiles(array $opts = array())
    {
        if (empty($opts['jsFilesBuild'])) {

            // Standard bootstrap files
            $jsFiles =  array();
            $jsFiles[] = $this->vendorsBaseUrl . '/requirejs/require.js';
            $jsFiles[] = $this->javascriptsBaseUrl . '/requirejs-init.js';

            // Do we have a "all-in-one" app JS file?
            if (
                !empty($this->app->vars['config']['optimization']['use_compiled_js_if_available']) &&
                file_exists($this->app->vars['app.var_path'] . '/assets/app-core.js')
            ) {
                $jsFiles[] = $this->app->vars['app.root_url'] . '/' . $this->app->appPath($this->app->vars['app.var_path']) . '/assets/app-core.js';
            }

            $jsFiles[] = $this->javascriptsBaseUrl . '/main.js';

        } else {

            // Specific "JS files build" bootstrap files
            $jsFiles =  array(
                $this->app->vars['app.root_url'] . '/node_modules/requirejs/bin/r.js',
                //$this->app->vars['app.root_url'] . '/node_modules/requirejs/require.js',
                $this->javascriptsBaseUrl . '/requirejs-init.js',
                $this->javascriptsBaseUrl . '/main.js',
            );

        }

        return $jsFiles;
    }

    /**
     * @inheritdoc
     */
    public function getViewExtensions()
    {
        return array(
          new Plates\Extension\App(),
          new Plates\Extension\AppAssets(),
        );
    }

    /**
     * @inheritdoc
     */
    public function getTemplatesFolders()
    {
        return array(
            array(
                'namespace' => 'core',
                'path' => $this->getAbsPath() . '/php/Core/Resources/templates'
            )
        );
    }

}