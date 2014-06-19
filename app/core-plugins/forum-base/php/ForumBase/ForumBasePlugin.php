<?php

namespace TalkTalk\CorePlugin\ForumBase;

use TalkTalk\Kernel\Plugin\PluginBase;
use TalkTalk\Kernel\Plugin\PluginInterface;

class ForumBasePlugin extends PluginBase
{

    protected $pluginId = 'forum-base';
    protected $pluginType = PluginInterface::PLUGIN_TYPE_CORE_PLUGIN;

    public function registerRestResources()
    {
        parent::registerRestResources();

        $NS = 'TalkTalk\\CorePlugin\\ForumBase\\Controller';
        $this->app->addRestResource('GET', '/api/forums', "$NS\\ForumBaseApiController::forums");
    }

    /**
     * @inheritdoc
    public function getJsModulesToCompile()
    {
        $myJsAmdModulesRootPath = $this->app->vars['app.root_path'] . '/' . $this->path . '/assets/js/amd';
        $myJsAmdModulesFilesPaths = $this->app
            ->get('utils.io')
            ->rglob('** /*.js', $myJsAmdModulesRootPath . '/mixins');

        $app = &$this->app;
        $myJsAmdModulesIds = array_map(
            function ($jsFilePath) use ($app) {
                return preg_replace('~\.js$~', '', $app->appPath($jsFilePath));
            },
            $myJsAmdModulesFilesPaths
        );

        return $myJsAmdModulesIds;
    }
     */


}