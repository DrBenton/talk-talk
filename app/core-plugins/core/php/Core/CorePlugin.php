<?php

namespace TalkTalk\CorePlugin\Core;

use TalkTalk\CorePlugin\Core\Controller\BaseController;
use TalkTalk\Kernel\Plugin\PluginBase;
use TalkTalk\Kernel\ApplicationInterface;

class CorePlugin extends PluginBase
{

    public function setApplication(ApplicationInterface $app)
    {
        parent::setApplication($app);

        BaseController::setApplication($this->app);
    }

    public function registerServices()
    {
        $app = &$this->app;
        $this->app->defineService('view', function () use ($app) {
            $service = new Service\View();
            $service->setTemplatesFilesExtension('tpl.php');

            return $service;
        });
    }

    public function registerRestResources()
    {
        parent::registerRestResources();

        $app = &$this->app;
        $app->addRestResource('GET', '/', 'TalkTalk\\CorePlugin\\Core\\Controller\\HomeController::home');
    }

    public function registerHooks()
    {
        parent::registerHooks();

        // JS and CSS files & data
        $this->app->get('hooks')->onHook(
            'layout.js.get_data',
            array($this, 'onHookLayoutGetJsData'),
            ApplicationInterface::EARLY_EVENT
        );
        $this->app->get('hooks')->onHook(
            'layout.js.get_scripts',
            array($this, 'onHookLayoutGetJsScripts'),
            ApplicationInterface::EARLY_EVENT
        );

        // JS files to compile for production
        $this->app->get('hooks')->onHook(
            'layout.js.get_files_to_compile',
            array($this, 'onHookLayoutGetJsFilesToCompile')
        );

        // View stuff
        $this->app->get('hooks')->onHook(
            'view.get_extensions',
            array($this, 'onHookViewGetExtensions')
        );
        $this->app->get('hooks')->onHook(
            'view.get_templates_folders',
            array($this, 'onHookViewGetTemplatesFolders')
        );

    }

    /**
     * @private
     */
    public function onHookLayoutGetJsData()
    {
        return array(
          'debug' => $this->app->vars['debug'],
          'rootUrl' => $this->app->vars['app.root_url'],
          'vendorsRootUrl' => $this->vendorsBaseUrl,
          'bootModules' => array(),
        );
    }

    /**
     * @private
     */
    public function onHookLayoutGetJsScripts(array $opts)
    {
        if (empty($opts['jsFilesBuild'])) {
            // Standard bootstrap files
            $jsFiles =  array(
                $this->vendorsBaseUrl . '/requirejs/require.js',
                $this->javascriptsBaseUrl . '/requirejs-init.js',
            );
            if (file_exists($this->app->vars['app.var_path'] . '/assets/app-core.js')) {
                $jsFiles[] = $this->app->vars['app.root_url'] . '/' . $this->app->appPath($this->app->vars['app.var_path']) . '/assets/app-core.js';
            }
            $jsFiles[] = $this->javascriptsBaseUrl . '/main.js';
        } else {
            // Specific "JS files build" bootstrap files
            $jsFiles =  array(
                $this->app->vars['app.root_url'] . '/node_modules/requirejs/bin/r.js',
                $this->app->vars['app.root_url'] . '/node_modules/requirejs/require.js',
                $this->javascriptsBaseUrl . '/requirejs-init.js',
                $this->javascriptsBaseUrl . '/main.js',
            );
        }

        return $jsFiles;
    }

    /**
     * @private
     */
    public function onHookLayoutGetJsFilesToCompile()
    {
        $myJsAmdModulesRootUrl = $this->path . '/assets/js/amd';

        $myJsAmdModulesIds = array(
            'app/core/vars-registry',
            'app/core/csrf-handler',
            'app/core/components/data/components-factory',
            'app/core/components/ui/layout-init-handler'
        );

        return $myJsAmdModulesIds;
    }

    /**
     * @private
     */
    public function onHookViewGetExtensions()
    {
        return array(
          new Plates\Extension\App(),
          new Plates\Extension\AppAssets(),
        );
    }

    /**
     * @private
     */
    public function onHookViewGetTemplatesFolders()
    {
        return array(
            array(
                'namespace' => 'core',
                'path' => $this->getAbsPath() . '/php/Core/Resources/templates'
            )
        );
    }

}