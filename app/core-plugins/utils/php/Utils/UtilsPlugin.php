<?php

namespace TalkTalk\CorePlugin\Utils;

use TalkTalk\Kernel\Plugin\PluginBase;
use TalkTalk\Kernel\ApplicationInterface;

class UtilsPlugin extends PluginBase
{

    public function registerRestResources()
    {
        parent::registerRestResources();

        if ($this->app->vars['debug']) {
            $this->app->addRestResource('GET', '/utils/phpinfo', 'TalkTalk\\CorePlugin\\Utils\\Controller\\UtilsController::phpinfo');
        }

        $this->app->addRestResource('GET', '/utils/js-app-compilation', 'TalkTalk\\CorePlugin\\Utils\\Controller\\UtilsController::compileJsApp');
        $this->app->addRestResource('POST', '/utils/js-app-compilation', 'TalkTalk\\CorePlugin\\Utils\\Controller\\UtilsController::saveJsAppCompilation');
    }


    public function registerHooks()
    {
        parent::registerHooks();

        // JS files to compile for production
        $this->app->get('hooks')->onHook(
            'layout.js.get_files_to_compile',
            array($this, 'onHookLayoutGetJsFilesToCompile')
        );

        // View stuff
        $this->app->get('hooks')->onHook(
            'view.get_templates_folders',
            array($this, 'onHookViewGetTemplatesFolders')
        );
    }

    /**
     * @private
     */
    public function onHookLayoutGetJsFilesToCompile()
    {
        $myJsAmdModulesRootPath = $this->app->vars['app.root_path'] . '/' . $this->path . '/assets/js/amd';
        $myJsAmdModulesFilesPaths = $this->app
            ->get('utils.io')
            ->rglob('**/*.js', $myJsAmdModulesRootPath . '/mixins');

        $app = &$this->app;
        $myJsAmdModulesIds = array_map(
            function ($jsFilePath) use ($app) {
                return preg_replace('~\.js$~', '', $app->appPath($jsFilePath));
            },
            $myJsAmdModulesFilesPaths
        );

        return $myJsAmdModulesIds;
    }

    /**
     * @private
     */
    public function onHookViewGetTemplatesFolders()
    {
        return array(
            array(
                'namespace' => 'utils',
                'path' => $this->getAbsPath() . '/php/Utils/Resources/templates'
            )
        );
    }


}