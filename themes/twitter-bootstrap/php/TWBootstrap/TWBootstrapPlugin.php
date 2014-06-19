<?php

namespace TalkTalk\Theme\TWBootstrap;

use TalkTalk\Kernel\Plugin\PluginBase;
use TalkTalk\Kernel\Plugin\PluginInterface;
use TalkTalk\Kernel\ApplicationInterface;

class TWBootstrapPlugin extends PluginBase
{

    protected $pluginId = 'twitter-bootstrap';
    protected $pluginType = PluginInterface::PLUGIN_TYPE_THEME;
    protected $jsFilesToCompilePriority = ApplicationInterface::LATE_EVENT;

    public function getHtmlPageCssFiles(array $opts = array())
    {
        return array(
          $this->assetsBaseUrl . '/bower_components/bootstrap/dist/css/bootstrap.min.css',
          $this->assetsBaseUrl . '/css/theme-twbootstrap.css',
        );
    }


    /**
     * @private
     */
    public function getJsModulesToCompile()
    {
        $myJsAssetsRootUrl = $this->path . '/assets/js';

        $myJsAmdModulesIds = parent::getJsModulesToCompile();

        $myJsAmdModulesIds = array_merge($myJsAmdModulesIds, array(
            $myJsAssetsRootUrl . '/templates-to-compile'
        ));

        return $myJsAmdModulesIds;
    }

}